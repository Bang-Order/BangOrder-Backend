<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\LoginRequest;
use App\Http\Resources\Auth\LoginResource;
use App\Http\Resources\Restaurant\RestaurantResource;
use App\Restaurant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function __construct() {
        $this->middleware('auth:sanctum')->only(['auth', 'logout']);
    }

    public function login(LoginRequest $request) {
        $credentials = $request->only('email', 'password');
        //dd($credentials);
        if (!Auth::attempt($credentials)) {
            return response()->json([
                'message' => 'Invalid login details'
            ], 401);
        }

        $restaurant = Restaurant::where('email', $request->email)->firstOrFail();
        $token = $restaurant->createToken('auth_token_restaurant_id_' . $restaurant->id)->plainTextToken;

        return new LoginResource(['restaurant' => $restaurant, 'token' => $token]);
    }

    public function register() {
        //
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
