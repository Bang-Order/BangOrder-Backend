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
use Kreait\Firebase\Storage;
use Intervention\Image\Facades\Image;

class MenuController extends Controller
{
    public function __construct(Storage $storage) {
        $this->middleware(['auth:sanctum', 'verified'])->only(['store', 'update', 'destroy']);
        $this->bucket = $storage->getBucket();
        $this->imageController = app('App\Http\Controllers\ImageController');
    }

    public function index(Restaurant $restaurant, Request $request) {
        if ($request->menu_category_id) {
            $menuCategory = $restaurant->menuCategories()->find($request->menu_category_id);
            if (!$menuCategory) {
                return response()->json([
                    'message' => 'ID Menu Kategori tidak valid'], 404
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

        return new MenuCollection($data->with('menuCategory')->get());
    }

    public function store(Restaurant $restaurant, MenuRequest $request) {
        $menu_category = $restaurant->menuCategories()->find($request->menu_category_id);
        if ($request->menu_category_id && !$menu_category) {
            return response()->json([
                'message' => 'ID Menu Kategori tidak valid'], 404
            );
        }

        if ($request->is_recommended == 1 || $request->is_recommended == true) {
            $count = $restaurant->menus()->where('is_recommended', 1)->count();
            if ($count >= 4) {
                return response()->json([
                    'message' => 'Menu rekomendasi hanya boleh memiliki maksimal 4 menu'], 422
                );
            }
        }

        $inserted_data = $restaurant->menus()->create($request->except('image'));

        if (empty($inserted_data)) {
            return response()->json(['message' => 'Gagal menambah data'], 400);
        } else {
            if ($request->hasFile('image')) {
                $imagePath = "id_$restaurant->id/menu/menu_id_$inserted_data->id.jpg";
                $streamedImage = $this->imageController->cropImage($request->file('image'));
                $imageLink = $this->imageController->uploadImage($streamedImage, $imagePath);
                $inserted_data->update(['image' => $imageLink]);
            }
            return response()->json([
                'message' => 'Data berhasil ditambahkan',
                'data' => new MenuResource($inserted_data->refresh())
            ], 201);
        }
    }

    public function show(Restaurant $restaurant, Menu $menu) {
        if ($restaurant->cannot('view', [$menu, $restaurant->id])) {
            return response()->json(['message' => 'Anda tidak diizinkan untuk melakukan aksi ini'], 401);
        }
        return new MenuResource($menu);
    }

    public function update(Restaurant $restaurant, Menu $menu, MenuRequest $request) {
        if ($request->menu_category_id) {
            $menu_category = $restaurant->menuCategories()->find($request->menu_category_id);
            if (!$menu_category) {
                return response()->json([
                    'message' => 'ID Menu Kategori tidak valid'], 404
                );
            }
        }

        if (($request->is_recommended == 1 || $request->is_recommended == true) && $menu->is_recommended != 1) {
            $count = $restaurant->menus()->where('is_recommended', 1)->count();
            if ($count >= 4) {
                return response()->json([
                    'message' => 'Menu rekomendasi hanya boleh memiliki maksimal 4 menu'], 422
                );
            }
        }

        $newrequest = $request->validated();
        if ($request->hasFile('image')) {
            $imagePath = "id_$restaurant->id/menu/menu_id_$menu->id.jpg";
            $streamedImage = $this->imageController->cropImage($request->file('image'));
            $imageLink = $this->imageController->uploadImage($streamedImage, $imagePath);
            $newrequest['image'] = $imageLink;
        }

        $updated_data = $menu->update($newrequest);
        if ($updated_data) {
            return response()->json(['message' => 'Data berhasil diupdate', 'data' => $menu]);
        } else {
            return response()->json(['message' => 'Gagal mengupdate data'], 400);
        }
    }

    public function destroy(Restaurant $restaurant, Menu $menu){
        if ($restaurant->cannot('delete', [$menu, $restaurant->id])) {
            return response()->json(['message' => 'Anda tidak diizinkan untuk melakukan aksi ini'], 401);
        }
        $deleted_data = $menu->delete();
        if ($deleted_data) {
            $imagePath = "id_$restaurant->id/menu/menu_id_$menu->id.jpg";
            $this->imageController->deleteImage($imagePath);
            return response()->json(['message' => 'Data berhasil dihapus']);
        } else {
            return response()->json(['message' => 'Gagal menghapus data'], 400);
        }
    }
}
