<?php

namespace App\Http\Controllers;

use App\Http\Requests\Restaurant\RestaurantRequest;
use App\Http\Resources\Restaurant\RestaurantDashboardResource;
use App\Http\Resources\Restaurant\RestaurantResource;
use App\Restaurant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Gate;
use Intervention\Image\Facades\Image;

class RestaurantController extends Controller
{
    public function __construct() {
        $this->middleware('auth:sanctum')->only('show', 'showDashboard', 'update');
    }

    public function index()
    {
        return response()->json(['message' => 'Please specify the Restaurant ID'], 405);
    }

    public function store(Request $request)
    {
        return response()->json(['message' => 'There are no POST method in RestaurantController'], 405);
    }

    public function show(Request $request, Restaurant $restaurant)
    {
        if (Gate::denies('restaurant-auth', $restaurant)) {
            return response()->json(['message' => 'This action is unauthorized.'], 401);
        }
        return new RestaurantResource($restaurant);
    }

    public function showDashboard(Request $request, Restaurant $restaurant) {
        if (Gate::denies('restaurant-auth', $restaurant)) {
            return response()->json(['message' => 'This action is unauthorized.'], 401);
        }

        $data = $restaurant->orders()
            ->selectRaw("DATE_FORMAT(created_at, '%d-%m-%Y') date, count(*) order_count, sum(total_price) total_income")
            ->where('order_status', '<>', 'payment_pending')
            ->groupBy('date');

        $todayData = with(clone $data)->first();
        if ($todayData->date != date('d-m-Y')) {
            $todayData->date = date('d-m-Y');
            $todayData->order_count = 0;
            $todayData->total_income = 0;
        }

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

        return response()->json([
            'today_data' => new RestaurantDashboardResource($todayData),
            'data' => RestaurantDashboardResource::collection($data->get())
        ]);
    }

    public function update(RestaurantRequest $request, Restaurant $restaurant)
    {
        if ($request->hasFile('image')) {
            $image_path = $this->saveImage($restaurant->id, $request->file('image'));
            $newrequest = $request->validated();
            $newrequest['image'] = asset($image_path);
        } else {
            $newrequest = $request->validated();
        }

        $updatedData = $restaurant->update($newrequest);

        if ($updatedData) {
            return response()->json([
                'message' => 'Data successfully updated',
                'data' => new RestaurantResource($restaurant)
            ]);
        } else {
            return response()->json(['message' => 'Update failed'], 400);
        }
    }

    public function destroy(Restaurant $restaurant)
    {
        return response()->json(['message' => 'There are no DELETE method in RestaurantController'], 405);
    }

    /**
     * @param string $restaurant_id
     * @param $image
     * @return string
     */
    public function saveImage(string $restaurant_id, $image): string
    {
        $image_directory_path = "storage/id_$restaurant_id";
        $image_save_path = "$image_directory_path/restaurant_id_$restaurant_id.jpg";

        if (!File::exists($image_directory_path)) {
            File::makeDirectory($image_directory_path);
        }

        list($width, $height) = getimagesize($image);
        if ($width != $height) {
            if ($width < $height) {
                $size = $width;
            } else {
                $size = $height;
            }
            Image::make($image)->fit($size)->save($image_save_path);
        } else {
            Image::make($image)->save($image_save_path);
        }
        return $image_save_path;
    }
}
