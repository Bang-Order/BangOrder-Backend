<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrderItemRequest;
use App\Http\Requests\OrderRequest;
use App\Http\Resources\Order\OrderCollection;
use App\Order;
use App\OrderItem;
use App\Restaurant;
use App\RestaurantTable;
use Faker\Provider\Uuid;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    public function index(Order $order, Request $request)
    {
        $query = $order->with('orderItems')->where('restaurant_id', $request->restaurant)->get();
        return new OrderCollection($query);
    }

    public function store(Order $order, OrderRequest $request, Restaurant $restaurant)
    {
        // $data = $order->create([
        //     'restaurant_id' => $request->restaurant,
        //     'restaurant_table_id' => $request->table_id,
        //     'total_price' => $request->total_price,
        //     'transaction_id' => Str::uuid(),
        //     'order_status' => 'antri',
        //     'payment_status' => 'pending'
        // ]);

        // $data = $restaurant->orders->create($request->validated());

        // if ($data) {
        //     return response()->json(['message' => 'a'], 200);
        // }
        // return response()->json(['message' => 'b'], 200);

        // if ($data) {
        //     return response()->json(['message' => 'a'], 200);
        // }
        // return response()->json(['message' => 'b'], 200);
    }

    public function show(Order $order)
    {
        //  
    }

    public function update(Request $request, Order $order)
    {
        //
    }

    public function destroy(Order $order)
    {
        //
    }
}
