<?php

namespace App\Http\Controllers;

use App\Http\Requests\MenuCategoryRequest;
use App\Http\Resources\MenuCategory\MenuCategoryCollection;
use App\Http\Resources\MenuCategory\MenuCategoryResource;
use App\Http\Resources\MenuCategoryWithMenu\MenuCategoryWithMenuCollection;
use App\MenuCategory;
use App\Restaurant;
use Illuminate\Http\Request;

class MenuCategoryController extends Controller
{
    public function index(Restaurant $restaurant) {
        return new MenuCategoryCollection($restaurant->menuCategories()->get());
    }

    public function indexWithMenu(Restaurant $restaurant) {
        return new MenuCategoryWithMenuCollection($restaurant->menuCategories()->with('menus')->get());
    }

    public function store(Restaurant $restaurant, MenuCategoryRequest $request) {
        $inserted_data = $restaurant->menuCategories()->create($request->validated());

        if (empty($inserted_data)) {
            return response()->json(['message' => 'Insert failed'], 400);
        } else {
            return response()->json(['message' => 'Data successfully added', 'data' => $inserted_data], 201);
        }
    }

    public function show(Restaurant $restaurant, MenuCategory $menuCategory) {
        if ($restaurant->id != $menuCategory->restaurant_id) {
            return response()->json(['message' => 'Restaurant ID and Menu Category Foreign Key does not match'], 404);
        }

        return new MenuCategoryResource($menuCategory);
    }

    public function update(Restaurant $restaurant, MenuCategory $menuCategory, MenuCategoryRequest $request) {
        if ($restaurant->id != $menuCategory->restaurant_id) {
            return response()->json(['message' => 'Restaurant ID and Menu Category Foreign Key does not match'], 404);
        }

        $updated_data = $menuCategory->update($request->validated());
        if ($updated_data) {
            return response()->json(['message' => 'Data successfully updated', 'data' => $menuCategory]);
        } else {
            return response()->json(['message' => 'Update failed'], 400);
        }
    }

    public function destroy(Restaurant $restaurant, MenuCategory $menuCategory) {
        if ($restaurant->id != $menuCategory->restaurant_id) {
            return response()->json(['message' => 'Restaurant ID and Menu Category Foreign Key does not match'], 404);
        }

        $deleted_data = $menuCategory->delete();
        if ($deleted_data) {
            return response()->json(['message' => 'Data successfully deleted']);
        } else {
            return response()->json(['message' => 'Delete failed'], 400);
        }
    }
}
