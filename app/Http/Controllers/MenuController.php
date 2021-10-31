<?php

namespace App\Http\Controllers;

use App\Http\Requests\Menu\MenuRequest;
use App\Http\Resources\Menu\MenuCollection;
use App\Http\Resources\Menu\MenuResource;
use App\Menu;
use App\Restaurant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MenuController extends Controller
{
    public function __construct() {
        $this->middleware('auth:sanctum')->only(['store', 'update', 'destroy']);
    }

    public function index(Restaurant $restaurant, Request $request) {
        if ($request->menu_category_id) {
            $menuCategory = $restaurant->menuCategories()->find($request->menu_category_id);
            if (!$menuCategory) {
                return response()->json([
                    'message' => 'Menu Category ID is invalid'], 404
                );
            }
            $data = $menuCategory->menus();
        } else {
            $data = $restaurant->menus();
        }
        if ($request->search) {
            $data = $data->where(
                DB::raw('lower(name)'), 'like', '%' . strtolower($request->search) . '%'
            );
        }
        if ($request->filter) {
            switch ($request->filter) {
                case 'recommendation' :
                    $data = $data->where('is_recommended', 1);
                    break;
                case 'available' :
                    $data = $data->where('is_available', 1);
                    break;
                case 'unavailable' :
                    $data = $data->where('is_available', 0);
            }
        }

        return new MenuCollection($data->get());
    }

    public function store(Restaurant $restaurant, MenuRequest $request) {
        $menu_category = $restaurant->menuCategories()->find($request->menu_category_id);
        if (!$menu_category) {
            return response()->json([
                'message' => 'Menu Category ID is invalid'], 404
            );
        }

        $inserted_data = $restaurant->menus()->create($request->validated());

        if (empty($inserted_data)) {
            return response()->json(['message' => 'Insert failed'], 400);
        } else {
            return response()->json([
                'message' => 'Data successfully added',
                'data' => new MenuResource($inserted_data)
            ], 201);
        }
    }

    public function show(Restaurant $restaurant, Menu $menu) {
        if ($restaurant->cannot('view', [$menu, $restaurant->id])) {
            return response()->json(['message' => 'This action is unauthorized.'], 401);
        }
        return new MenuResource($menu);
    }

    public function update(Restaurant $restaurant, Menu $menu, MenuRequest $request) {
        if ($request->menu_category_id) {
            $menu_category = $restaurant->menuCategories()->find($request->menu_category_id);
            if (!$menu_category) {
                return response()->json([
                    'message' => 'Menu Category ID is invalid'], 404
                );
            }
        }

        $updated_data = $menu->update($request->validated());
        if ($updated_data) {
            return response()->json(['message' => 'Data successfully updated', 'data' => $menu]);
        } else {
            return response()->json(['message' => 'Update failed'], 400);
        }
    }

    public function destroy(Restaurant $restaurant, Menu $menu){
        if ($restaurant->cannot('delete', [$menu, $restaurant->id])) {
            return response()->json(['message' => 'This action is unauthorized.'], 401);
        }
        $deleted_data = $menu->delete();
        if ($deleted_data) {
            return response()->json(['message' => 'Data successfully deleted']);
        } else {
            return response()->json(['message' => 'Delete failed'], 400);
        }
    }
}
