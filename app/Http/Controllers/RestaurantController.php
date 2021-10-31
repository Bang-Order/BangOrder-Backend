<?php

namespace App\Http\Controllers;

use App\Http\Requests\Restaurant\RestaurantRequest;
use App\Http\Resources\Restaurant\RestaurantResource;
use App\Restaurant;
use Illuminate\Http\Request;

class RestaurantController extends Controller
{
    public function __construct() {
        $this->middleware('auth:sanctum')->only('show', 'update');
    }

    public function index()
    {
        return response()->json(['message' => 'Please specify the Restaurant ID'], 405);
    }

    public function store(Request $request)
    {
        return response()->json(['message' => 'There are no POST method in RestaurantController'], 405);
    }

    public function show(Restaurant $restaurant)
    {
        return new RestaurantResource($restaurant);
    }

    public function update(RestaurantRequest $request, Restaurant $restaurant)
    {
        $updatedData = $restaurant->update($request->validated());
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
}
