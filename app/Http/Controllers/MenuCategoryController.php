<?php

namespace App\Http\Controllers;

use App\Http\Requests\MenuCategory\MenuCategoryRequest;
use App\Http\Resources\MenuCategory\MenuCategoryCollection;
use App\Http\Resources\MenuCategory\MenuCategoryResource;
use App\Http\Resources\MenuCategoryWithMenu\MenuCategoryWithMenuCollection;
use App\MenuCategory;
use App\Restaurant;
use Exception;
use Illuminate\Http\Request;

class MenuCategoryController extends Controller
{
    public function __construct() {
        $this->middleware(['auth:sanctum', 'verified'])->only(['store', 'update', 'destroy']);
    }

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
            return response()->json([
                'message' => 'Data successfully added',
                'data' => new MenuCategoryResource($inserted_data)
            ], 201);
        }
    }

    public function show(Restaurant $restaurant, MenuCategory $menu_category) {
        if ($restaurant->cannot('view', [$menu_category, $restaurant->id])) {
            return response()->json(['message' => 'This action is unauthorized.'], 401);
        }
        return new MenuCategoryResource($menu_category);
    }

    public function update(Restaurant $restaurant, MenuCategory $menu_category, MenuCategoryRequest $request) {
        $updated_data = $menu_category->update($request->validated());
        if ($updated_data) {
            return response()->json([
                'message' => 'Data successfully updated',
                'data' => new MenuCategoryResource($menu_category)
            ]);
        } else {
            return response()->json(['message' => 'Update failed'], 400);
        }
    }

    public function destroy(Restaurant $restaurant, MenuCategory $menu_category) {
        if ($restaurant->cannot('delete', [$menu_category, $restaurant->id])) {
            return response()->json(['message' => 'This action is unauthorized.'], 401);
        }

        $deleted_data = $menu_category->delete();
        if ($deleted_data) {
            return response()->json(['message' => 'Data successfully deleted']);
        } else {
            return response()->json(['message' => 'Delete failed'], 400);
        }
    }
}
