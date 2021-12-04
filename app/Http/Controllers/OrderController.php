<?php

namespace App\Http\Controllers;

use App\Http\Requests\Order\StoreOrderRequest;
use App\Http\Requests\Order\UpdateOrderRequest;
use App\Http\Resources\Order\OrderCollection;
use App\Http\Resources\Order\OrderResource;
use App\Order;
use App\Restaurant;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Kreait\Firebase\Database;
use Kreait\Firebase\Exception\DatabaseException;
use Xendit\Invoice;
use Xendit\Xendit;

class OrderController extends Controller
{
    public function __construct(Database $database) {
        $this->middleware('auth:sanctum')->only(['index', 'indexAll', 'update', 'destroy']);
        $this->database = $database;
    }

    public function index(Request $request, Restaurant $restaurant)
    {
        if ($request->user()->cannot('viewAny', [Order::class, $restaurant->id])) {
            return response()->json(['message' => 'This action is unauthorized.'], 401);
        }

        if ($request->status) {
            $request->validate(['status' => Rule::in(['antri', 'dimasak', 'selesai'])]);
            $data = $restaurant->orders()->where('order_status', $request->status);
        } else {
            $data = $restaurant->orders()->whereIn('order_status', ['antri', 'dimasak']);
        }

        return new OrderCollection($data->get());
    }

    public function indexAll(Restaurant $restaurant, Request $request) {
        if ($request->user()->cannot('viewAny', [Order::class, $restaurant->id])) {
            return response()->json(['message' => 'This action is unauthorized.'], 401);
        }

        $data = $restaurant->orders()->where('order_status', '<>', 'payment_pending');
        if ($start_date = $request->start_date) {
            $request->validate([
                'start_date' => 'date_format:Y-m-d',
                'end_date' => 'date_format:Y-m-d'
            ]);
            $end_date = $request->end_date ?: now()->toDateString();
            if (strtotime($start_date) <= strtotime($end_date)) {
                $data = $data->whereBetween('created_at', [$start_date.' 00:00:00', $end_date.' 23:59:59']);
            }
            else {
                return response()->json(['message' => 'Start date value must lower than end date'], 422);
            }
        }
        return new OrderCollection($data->get());
    }

    public function indexArray(Request $request) {
        $request->validate(['order_id' => 'array']);
        $data = Order::whereIn('id', $request->order_id)->latest();
        return new OrderCollection($data->get());
    }

    public function store(Restaurant $restaurant, StoreOrderRequest $request)
    {
        $restaurantTable = $restaurant->restaurantTables()->find($request->restaurant_table_id);
        if (!$restaurantTable) {
            return response()->json([
                'message' => 'Restaurant Table ID is invalid'
            ], 404);
        }

        $request->merge(['transaction_id' => time()]);
        $inserted_order = $restaurant->orders()->create($request->all());

        if (empty($inserted_order)) {
            return response()->json(['message' => 'Insert Order failed'], 400);
        } else {
            //insert each of order items
            $sync_data = [];
            $items = [];
            foreach ($request->order_items as $item) {
                $validator = Validator::make($item, [
                    'menu_id' => ['required', 'integer', 'exists:App\Menu,id'],
                    'quantity' => ['required', 'integer', 'gte:1'],
                    'notes' => ['nullable', 'string']
                ]);
                if ($validator->fails()) {
                    $inserted_order->delete();
                    return response()->json([
                        'message' => 'The given data was invalid.',
                        'errors' => $validator->errors()
                    ], 422);
                }

                $menu = $restaurant->menus()->find($item['menu_id']); //might want to optimize this later to avoid repetitive query call
                if (!$menu) {
                    $inserted_order->delete();
                    return response()->json(['message' => 'Restaurant ID and Menu Foreign Key does not match'], 404);
                }

                $sync_data[$item['menu_id']] = [
                    'quantity' => $item['quantity'],
                    'notes' => array_key_exists('notes', $item) ? $item['notes'] : null
                ];
                $items[] = json_encode([
                    'name' => $menu->name,
                    'quantity' => $item['quantity'],
                    'price' => $menu->price
                ]);
            }

            $inserted_item = $inserted_order->menus()->sync($sync_data);
            if (empty($inserted_item['attached'])) {
                $inserted_order->delete();
                return response()->json(['message' => 'Insert OrderItem failed'], 400);
            } else {
                // Create xendit invoice charge
                try {
                    Xendit::setApiKey(env('XENDIT_API_KEY'));
                    $params = [
                        'external_id' => "Bang Order - $restaurant->name - $inserted_order->id",
                        'amount' => $request->total_price,
                        'description' => "Pembayaran ke $restaurant->name via Bang Order",
                        'items' => $items
                    ];
                    $createInvoice = Invoice::create($params);
                    $inserted_order->update([
                        'transaction_id' => $createInvoice['id'],
                        'invoice_url' => $createInvoice['invoice_url']
                    ]);
                } catch (Exception $e) {
                    $inserted_order->delete();
                    return response()->json(['message' => 'Xendit error: ' . $e->getMessage()], $e->getCode());
                }

                $orderResource = new OrderResource($inserted_order->refresh()->load('orderItems.menu'));
                //filter resource to insert it into realtime databse
                $filteredKey  = ['id', 'table_number', 'created_at', 'order_status', 'total_price', 'order_items'];
                $filteredResource = array_filter(
                    json_decode($orderResource->toJson(), true),
                    function ($key) use ($filteredKey) {
                        return in_array($key, $filteredKey);
                    },
                    ARRAY_FILTER_USE_KEY
                );

                //add order data into firebase realtime database
                try {
                    $this->database
                        ->getReference("orders/$restaurant->id/$inserted_order->id")
                        ->set($filteredResource);
                } catch (Exception $e) {
                    $inserted_order->delete();
                    return response()->json(['message' => 'Firebase Realtime Database error: ' . $e->getMessage()],
                        $e->getCode());
                }

                return response()->json([
                    'message' => 'Data successfully created',
                    'data' => $orderResource
                ], 201);
            }
        }
    }

    public function show(Restaurant $restaurant, Order $order)
    {
        if ($restaurant->cannot('view', [$order, $restaurant->id])) {
            return response()->json(['message' => 'This action is unauthorized.'], 401);
        }
        return new OrderResource($order);
    }

    public function update(Restaurant $restaurant, Order $order, UpdateOrderRequest $request)
    {
        $updated_data = $order->update($request->validated());
        if ($updated_data) {
            //update order status on firebase realtime database
            try {
                $referencePath = "orders/$restaurant->id/$order->id";
                if ($this->database->getReference($referencePath)->getSnapshot()->exists()) {
                    $order_status = $order->order_status;
                    switch ($order_status) {
                        case 'selesai':
                            $this->database
                                ->getReference($referencePath)
                                ->remove();
                            break;
                        default:
                            $this->database
                                ->getReference($referencePath)
                                ->getChild('order_status')
                                ->set($order_status);
                            break;
                    }
                }
            } catch (DatabaseException $e) {
                return response()->json(['message' => 'Firebase Realtime Database error: ' . $e->getMessage()],
                    $e->getCode());
            }
            return response()->json([
                'message' => 'Data successfully updated',
                'data' => new OrderResource($order)
            ]);
        } else {
            return response()->json(['message' => 'Update failed'], 400);
        }
    }

    public function destroy(Restaurant $restaurant, Order $order)
    {
        if ($restaurant->cannot('delete', [$order, $restaurant->id])) {
            return response()->json(['message' => 'This action is unauthorized.'], 401);
        }
        $deleted_data = $order->delete();
        if ($deleted_data) {
            //Delete an order from firebase realtime database
            try {
                $referencePath = "orders/$restaurant->id/$order->id";
                if ($this->database->getReference($referencePath)->getSnapshot()->exists()) {
                    $this->database
                        ->getReference($referencePath)
                        ->remove();
                }
            } catch (DatabaseException $e) {
                return response()->json(['message' => 'Firebase Realtime Database error: ' . $e->getMessage()],
                    $e->getCode());
            }
            return response()->json(['message' => 'Data successfully deleted']);
        } else {
            return response()->json(['message' => 'Delete failed'], 400);
        }
    }
}
