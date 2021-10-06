<?php

namespace App\Http\Controllers;

use App\Http\Requests\MenuRequest;
use App\Http\Resources\Menu\MenuCollection;
use App\Http\Resources\Menu\MenuResource;
use App\Menu;
use App\Restaurant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Validator as ValidationValidator;

class MenuController extends Controller
{
    public function index(Restaurant $restaurant, Request $request) {
        if ($request->search) {
            $data = $restaurant->menus()->where(
                DB::raw('lower(name)'), 'like', '%' . strtolower($request->search) . '%'
            )->get();
        } elseif ($request->menu_category_id) {
            $menuCategory = $restaurant->menuCategories()->find($request->menu_category_id);
            if (!$menuCategory) {
                return response()->json([
                    'message' => 'Menu Category ID is invalid'], 404
                );
            }
            $data = $menuCategory->menus()->get();
        } else {
            $data = $restaurant->menus()->get();
        }
        if ($data->isEmpty()) {
            return response()->json(['message' => 'Data Not Found!'], 404);
        }
        return new MenuCollection($data);
    }

    public function store(Restaurant $restaurant, MenuRequest $request) {
        $menuCategory = $restaurant->menuCategories()->find($request->menu_category_id);
        if (!$menuCategory) {
            return response()->json([
                'message' => 'Menu Category ID is invalid'], 404
            );
        }

        $inserted_data = $restaurant->menus()->create($request->validated());

        if (is_null($inserted_data)) {
            return response()->json(['message' => 'Insert failed'], 400);
        } else {
            return response()->json(['message' => 'Data successfully added', 'data' => $inserted_data], 201);
        }
    }

    public function show(Restaurant $restaurant, Menu $menu) {
        if ($restaurant->id != $menu->restaurant_id) {
            return response()->json(['message' => 'Restaurant ID and Menu Foreign Key does not match'], 404);
        }

        return new MenuResource($menu);
    }

    public function update(Restaurant $restaurant, Menu $menu, MenuRequest $request) {
        if ($restaurant->id != $menu->restaurant_id) {
            return response()->json(['message' => 'Restaurant ID and Menu Foreign Key does not match'], 404);
        }

        $menuCategory = $restaurant->menuCategories()->find($request->menu_category_id);
        if (!$menuCategory) {
            return response()->json([
                'message' => 'Menu Category ID is invalid'], 404
            );
        }

        $updated_data = $menu->update($request->validated());

        if ($updated_data) {
            return response()->json(['message' => 'Data successfully updated', 'data' => $menu]);
        } else {
            return response()->json(['message' => 'Update failed'], 400);
        }
    }

    public function destroy(Restaurant $restaurant, Menu $menu){
        if ($restaurant->id != $menu->restaurant_id) {
            return response()->json(['message' => 'Restaurant ID and Menu Foreign Key does not match'], 404);
        }

        $deleted_data = $menu->delete();

        if ($deleted_data) {
            return response()->json(['message' => 'Data successfully deleted']);
        } else {
            return response()->json(['message' => 'Delete failed'], 400);
        }
    }
}
