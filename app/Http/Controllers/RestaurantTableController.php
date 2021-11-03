<?php

namespace App\Http\Controllers;

use App\Http\Requests\RestaurantTable\RestaurantTableRequest;
use App\Http\Resources\RestaurantTable\RestaurantTableCollection;
use App\Http\Resources\RestaurantTable\RestaurantTableResource;
use App\Restaurant;
use App\RestaurantTable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class RestaurantTableController extends Controller
{
    public function __construct() {
        $this->middleware('auth:sanctum')->only(['index', 'getQRCode', 'store', 'update', 'destroy']);
    }

    public function index(Request $request, Restaurant $restaurant)
    {
        if ($request->user()->cannot('viewAny', [RestaurantTable::class, $restaurant->id])) {
            return response()->json(['message' => 'This action is unauthorized.'], 401);
        }
        return new RestaurantTableCollection($restaurant->restaurantTables()->get());
    }

    public function store(Restaurant $restaurant, RestaurantTableRequest $request)
    {
        $request->merge(['link' => time()]);
        $inserted_data = $restaurant->restaurantTables()->create($request->all());

        if (empty($inserted_data)) {
            return response()->json(['message' => 'Insert failed'], 400);
        } else {
            $restaurant_id = $restaurant->id;
            $table_id = $inserted_data->id;
            $qr_value = base64_encode("{\"restaurant_id\":\"$restaurant_id\", \"table_id\":\"$table_id\"}");
            $qr_directory_path = "storage/id_$restaurant_id/qr_code";
            $sticker_origin_path = 'assets/Sticker_QR_Code.jpg';
            $sticker_save_path = $qr_directory_path . "/qr_id_$table_id.jpg";
            $qr_api_link = "https://api.qrserver.com/v1/create-qr-code/?size=1000x1000&data=$qr_value";

            if (!File::exists($qr_directory_path)) {
                File::makeDirectory($qr_directory_path);
            }

            $qr_base64 = base64_encode(Http::get($qr_api_link));

            $img = Image::make($sticker_origin_path);
            $img->insert($qr_base64, 'center');
            $this->add_text($img, $restaurant->name, $inserted_data->table_number);
            $img->save($sticker_save_path);

            $inserted_data->update(['link' => asset($sticker_save_path)]);

            return response()->json([
                'message' => 'Data successfully added',
                'data' => new RestaurantTableResource($inserted_data)
            ], 201);
        }

    }

    public function show(Restaurant $restaurant, RestaurantTable $table)
    {
        if ($restaurant->cannot('view', [$table, $restaurant->id])) {
            return response()->json(['message' => 'This action is unauthorized.'], 401);
        }

        return response()->json([
            'restaurant_id' => $restaurant->id,
            'name' => $restaurant->name,
            'address' => $restaurant->address,
            'image' => $restaurant->image,
            'table_id' => $table->id,
            'table_number' => $table->table_number
        ]);
    }

    public function getQRCode(Restaurant $restaurant, RestaurantTable $table) {
        if ($restaurant->cannot('view', [$table, $restaurant->id])) {
            return response()->json(['message' => 'This action is unauthorized.'], 401);
        }
        $sticker_path = "storage/id_$restaurant->id/qr_code/qr_id_$table->id.jpg";
        return response()->download($sticker_path);
    }

    public function update(RestaurantTableRequest $request, Restaurant $restaurant, RestaurantTable $table)
    {
        $updated_data = $table->update($request->validated());
        if ($updated_data) {
            $sticker_path = "storage/id_$restaurant->id/qr_code/qr_id_$table->id.jpg";

            $img = Image::make($sticker_path);
            $img->rectangle(0, 2000, 1748, 2480, function ($draw) {
                $draw->background('#FFC300');
            });
            $this->add_text($img, $restaurant->name, $table->table_number);
            $img->save($sticker_path);

            return response()->json([
                'message' => 'Data successfully updated',
                'data' => new RestaurantTableResource($table)
            ]);

        } else {
            return response()->json(['message' => 'Update failed'], 400);
        }
    }

    public function destroy(Restaurant $restaurant, RestaurantTable $table)
    {
        if ($restaurant->cannot('delete', [$table, $restaurant->id])) {
            return response()->json(['message' => 'This action is unauthorized.'], 401);
        }
        $deleted_data = $table->delete();
        if ($deleted_data) {
            $sticker_path = "storage/id_$restaurant->id/qr_code/qr_id_$table->id.jpg";
            if (File::exists($sticker_path)) {
                File::delete($sticker_path);
            }
            return response()->json(['message' => 'Data successfully deleted']);
        } else {
            return response()->json(['message' => 'Delete failed'], 400);
        }
    }

    public function add_text(\Intervention\Image\Image $img, string $restaurant_name, string $table_number): void
    {
        $img->text($restaurant_name, 874, 2080, function ($font) {
            $font->file(realpath('assets/Manrope-Bold.ttf'));
            $font->size(120);
            $font->align('center');
            $font->valign('center');
        });
        $img->text("Meja $table_number", 874, 2230, function ($font) {
            $font->file(realpath('assets/Manrope-Medium.ttf'));
            $font->size(100);
            $font->align('center');
            $font->valign('center');
        });
    }
}
