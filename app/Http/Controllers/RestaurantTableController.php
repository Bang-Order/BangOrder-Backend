<?php

namespace App\Http\Controllers;

use App\Http\Requests\RestaurantTable\RestaurantTableRequest;
use App\Http\Resources\RestaurantTable\RestaurantTableCollection;
use App\Http\Resources\RestaurantTable\RestaurantTableResource;
use App\Restaurant;
use App\RestaurantTable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Intervention\Image\Facades\Image;
use Kreait\Firebase\DynamicLink\AndroidInfo;
use Kreait\Firebase\DynamicLink\CreateDynamicLink;
use Kreait\Firebase\DynamicLink\CreateDynamicLink\FailedToCreateDynamicLink;
use Kreait\Firebase\DynamicLink\NavigationInfo;
use Kreait\Firebase\Storage;

class RestaurantTableController extends Controller
{
    public function __construct(Storage $storage) {
        $this->middleware(['auth:sanctum', 'verified'])->only(['index', 'store', 'update', 'destroy']);
        $this->bucket = $storage->getBucket();
        $this->imageController = app('App\Http\Controllers\ImageController');
    }

    public function index(Request $request, Restaurant $restaurant)
    {
        if ($request->user()->cannot('viewAny', [RestaurantTable::class, $restaurant->id])) {
            return response()->json(['message' => 'Anda tidak diizinkan untuk melakukan aksi ini'], 401);
        }
        return new RestaurantTableCollection($restaurant->restaurantTables()->get());
    }

    public function store(Restaurant $restaurant, RestaurantTableRequest $request)
    {
        $request->merge(['link' => time()]);
        $inserted_data = $restaurant->restaurantTables()->create($request->all());

        if (empty($inserted_data)) {
            return response()->json(['message' => 'Gagal menambah data'], 400);
        } else {
            $stickerSaveLink = $this->generateQrSticker($restaurant, $inserted_data);

            $inserted_data->update(['link' => $stickerSaveLink]);

            return response()->json([
                'message' => 'Data berhasil ditambah',
                'data' => new RestaurantTableResource($inserted_data)
            ], 201);
        }

    }

    public function show(Restaurant $restaurant, RestaurantTable $table)
    {
        if ($restaurant->cannot('view', [$table, $restaurant->id])) {
            return response()->json(['message' => 'Anda tidak diizinkan untuk melakukan aksi ini'], 401);
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
            return response()->json(['message' => 'Anda tidak diizinkan untuk melakukan aksi ini'], 401);
        }
        $table_id = $table->id;
        $link = $table->link;
        return response()->streamDownload(function () use ($link) {
            echo readfile($link);
        }, "qr_id_$table_id.jpg");
    }

    public function update(RestaurantTableRequest $request, Restaurant $restaurant, RestaurantTable $table)
    {
        $updated_data = $table->update($request->validated());
        if ($updated_data) {
            $imagePath = "id_$restaurant->id/qr_code/qr_id_$table->id.jpg";

            $img = Image::make($table->link);
            $img->rectangle(0, 2000, 1748, 2480, function ($draw) {
                $draw->background('#FFC300');
            });
            $this->addText($img, $restaurant->name, $table->table_number);
            $encodedImage = $img->stream('jpg');
            $this->imageController->uploadImage($encodedImage, $imagePath);

            return response()->json([
                'message' => 'Data berhawsil diupdate',
                'data' => new RestaurantTableResource($table)
            ]);

        } else {
            return response()->json(['message' => 'Gagal mengupdate data'], 400);
        }
    }

    public function destroy(Restaurant $restaurant, RestaurantTable $table)
    {
        if ($restaurant->cannot('delete', [$table, $restaurant->id])) {
            return response()->json(['message' => 'Anda tidak diizinkan untuk melakukan aksi ini'], 401);
        }
        $deleted_data = $table->delete();
        if ($deleted_data) {
            $imagePath = "id_$restaurant->id/qr_code/qr_id_$table->id.jpg";
            $this->imageController->deleteImage($imagePath);
            return response()->json(['message' => 'Data berhasil dihapus']);
        } else {
            return response()->json(['message' => 'Gagal menghapus data'], 400);
        }
    }

    /**
     * @param Restaurant $restaurant
     * @param Model $table
     * @return string
     */
    public function generateQrSticker(Restaurant $restaurant, Model $table)
    {
        $restaurant_id = $restaurant->id;
        $table_id = $table->id;
        $imagePath = "id_$restaurant_id/qr_code/qr_id_$table_id.jpg";
        $qr_value = $this->generateDynamicLink($restaurant_id, $table_id);
        $sticker_origin_path = 'assets/Sticker_QR_Code.jpg';
        $qr_api_link = "https://api.qrserver.com/v1/create-qr-code/?size=1000x1000&data=$qr_value";

        $qr_base64 = base64_encode(Http::get($qr_api_link));

        $img = Image::make($sticker_origin_path);
        $img->insert($qr_base64, 'center');
        $this->addText($img, $restaurant->name, $table->table_number);
        $encodedImage = $img->stream('jpg');

        $imageLink = $this->imageController->uploadImage($encodedImage, $imagePath);
        return $imageLink;
    }

    private function addText(\Intervention\Image\Image $img, string $restaurant_name, string $table_number): void
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

    /**
     * @param string $restaurant_id
     * @param string $table_id
     * @return mixed
     */
    private function generateDynamicLink(string $restaurant_id, string $table_id): string
    {
        try {
            $dynamicLinks = app('firebase.dynamic_links');
            $url = "https://www.google.com?restaurant_id=$restaurant_id&table_id=$table_id";
            $action = CreateDynamicLink::forUrl($url)
                ->withDynamicLinkDomain('https://bangorder.page.link')
                ->withAndroidInfo(AndroidInfo::new()->withPackageName('com.bangorder.mobile'))
                ->withNavigationInfo(NavigationInfo::new()->withForcedRedirect());
            $link = $dynamicLinks->createDynamicLink($action);
        } catch (FailedToCreateDynamicLink $e) {
            dd($e);
        }
        return (string) $link;
    }
}
