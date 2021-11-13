<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\Auth\LoginResource;
use App\Http\Resources\Auth\RegisterResource;
use App\Http\Resources\Restaurant\RestaurantResource;
use App\Restaurant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;

class AuthController extends Controller
{
    public function __construct() {
        $this->middleware('auth:sanctum')->only(['auth', 'logout']);
    }

    public function login(LoginRequest $request) {
        $credentials = $request->only('email', 'password');
        if (!Auth::attempt($credentials)) {
            return response()->json([
                'message' => 'Invalid login details'
            ], 401);
        }

        $restaurant = Restaurant::where('email', $request->email)->firstOrFail();
        $token = $restaurant->createToken('auth_token_restaurant_id_' . $restaurant->id)->plainTextToken;

        return response()->json([
            'message' => 'Login Success',
            'data' => new LoginResource(['restaurant' => $restaurant, 'token' => $token])
        ]);
    }

    public function register(RegisterRequest $request) {
        $request->request->add(['password' => Hash::make($request->password)]);
        $registered_data = Restaurant::create($request->except(['confirm_password', 'table_amount', 'image']));
        if (empty($registered_data)) {
            return response()->json(['message' => 'Register failed'], 400);
        }
        $restaurant_id = $registered_data->id;

        $restaurant_directory = "storage/id_$restaurant_id";
        if (!File::exists($restaurant_directory)) {
            File::makeDirectory($restaurant_directory);
            File::makeDirectory($restaurant_directory . '/menu');
            File::makeDirectory($restaurant_directory . '/qr_code');
        }

        if ($request->hasFile('image')) {
            $image_path = app('App\Http\Controllers\RestaurantController')
                ->saveImage($restaurant_id, $request->file('image'));
            $registered_data->update(['image' => asset($image_path)]);
        }

        $token = $registered_data->createToken('auth_token_restaurant_id_' . $restaurant_id)->plainTextToken;

//        for ($i = 1; $i <= $request->table_amount; $i++) {
//            $table_request = ['table_number' => $i, 'link' => time()];
//            $table_data = $registered_data->restaurantTables()->create($table_request);
//            if (empty($table_data)) {
//                return response()->json(['message' => 'Restaurant table insert failed'], 400);
//            }
//            $sticker_save_path = app('App\Http\Controllers\RestaurantTableController')
//                ->generateQrCode($registered_data, $table_data);
//            $table_data->update(['link' => asset($sticker_save_path)]);
//        }

        return response()->json([
            'message' => 'Register Success',
            'data' => new RegisterResource(['restaurant' => $registered_data->refresh(), 'token' => $token])
        ], 201);
    }

    public function auth(Request $request) {
        return response()->json([
            'message' => 'Authentication Success',
            'data' => new RestaurantResource($request->user())
        ]);
    }

    public function logout(Request $request) {
        $deleted_token = $request->user()->currentAccessToken()->delete();
        if ($deleted_token) {
            return response()->json(['message' => 'Logout Success']);
        } else {
            return response()->json(['message' => 'Delete failed'], 400);
        }
    }
}
