<?php

namespace App\Http\Controllers;

use App\Http\Requests\RestaurantRequest;
use App\Http\Resources\Restaurant\RestaurantCollection;
use App\Http\Resources\Restaurant\RestaurantResource;
use App\Restaurant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RestaurantController extends Controller
{
    public function index(Restaurant $restaurant)
    {
        //
    }

    public function store(Request $request)
    {
        //
    }

    public function show(Restaurant $restaurant)
    {
        return new RestaurantResource($restaurant);
    }

    public function update(RestaurantRequest $request, Restaurant $restaurant)
    {
        $updatedData = $restaurant->update($request->validated());
        // $data = $restaurant->update($request->validated());
        if ($updatedData) {
            return response()->json([
                'message' => 'Data successfully updated',
                'data' => $restaurant
            ]);
        } else {
            return response()->json(['message' => 'Update failed'], 400);
        }
    }

    public function destroy(Restaurant $restaurant)
    {
        //
    }
}
