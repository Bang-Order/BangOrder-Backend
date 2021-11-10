<?php

namespace App\Http\Controllers;

use App\Http\Requests\Menu\MenuRequest;
use App\Http\Resources\Menu\MenuCollection;
use App\Http\Resources\Menu\MenuResource;
use App\Menu;
use App\Restaurant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

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

        $inserted_data = $restaurant->menus()->create($request->except('image'));

        if (empty($inserted_data)) {
            return response()->json(['message' => 'Insert failed'], 400);
        } else {
            if ($request->hasFile('image')) {
                $image_path = $this->saveImage($restaurant->id, $inserted_data->id, $request->file('image'));
                $inserted_data->update(['image' => asset($image_path)]);
            }
            return response()->json([
                'message' => 'Data successfully added',
                'data' => new MenuResource($inserted_data->refresh())
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

        if ($request->hasFile('image')) {
            $image_path = $this->saveImage($restaurant->id, $menu->id, $request->file('image'));
            $newrequest = $request->validated();
            $newrequest['image'] = asset($image_path);
        } else {
            $newrequest = $request->validated();
        }

        $updated_data = $menu->update($newrequest);
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
        $restaurant_id = $restaurant->id;
        $menu_id = $menu->id;
        $deleted_data = $menu->delete();
        if ($deleted_data) {
            $image_path = "id_$restaurant_id/menu/menu_id_$menu_id.jpg";
            if (Storage::exists($image_path)) {
                Storage::delete($image_path);
            }
            return response()->json(['message' => 'Data successfully deleted']);
        } else {
            return response()->json(['message' => 'Delete failed'], 400);
        }
    }

    /**
     * @param string $restaurant_id
     * @param string $menu_id
     * @param $image
     * @return string
     */
    private function saveImage(string $restaurant_id, string $menu_id, $image): string
    {
        $image_directory_path = "storage/id_$restaurant_id/menu";
        $image_save_path = "$image_directory_path/menu_id_$menu_id.jpg";

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
