<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterAccountRequest;
use App\Http\Requests\Auth\RegisterProfileRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Http\Resources\Auth\LoginResource;
use App\Http\Resources\Auth\RegisterResource;
use App\Http\Resources\Restaurant\RestaurantResource;
use App\Restaurant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;

class AuthController extends Controller
{
    public function __construct() {
        $this->middleware('auth:sanctum')->only(['auth', 'logout', 'resendEmail']);
        $this->middleware('verified')->only('auth');
        $this->middleware('throttle:6,1')->only(['verifyEmail', 'resendEmail', 'sendResetPassword']);
    }

    public function login(LoginRequest $request) {
        $credentials = $request->only('email', 'password');
        if (!Auth::attempt($credentials)) {
            return response()->json([
                'message' => 'Email atau password salah, silahkan coba lagi'
            ], 401);
        }

        $restaurant = Restaurant::where('email', $request->email)->firstOrFail();
        $token = $restaurant->createToken('auth_token_restaurant_id_' . $restaurant->id)->plainTextToken;

        if (!$restaurant->hasVerifiedEmail()) {
            return response()->json([
                'message' => 'Akun Anda masih belum terverifikasi, silahkan melakukan verifikasi email terlebih dahulu dan coba lagi',
                'data' => new LoginResource(['restaurant' => $restaurant, 'token' => $token])
            ], 403);
        }
        return response()->json([
            'message' => 'Login berhasil',
            'data' => new LoginResource(['restaurant' => $restaurant, 'token' => $token])
        ]);
    }

    public function registerAccount(RegisterAccountRequest $request) {
        return response()->json([
            'message' => 'Akun tersedia',
            'data' => [
                'email' => $request->email,
                'password' => Hash::make($request->password)
            ]
        ]);
    }

    public function registerProfile(RegisterProfileRequest $request) {
        $registered_data = Restaurant::create($request->except(['image']));
        if (empty($registered_data)) {
            return response()->json(['message' => 'Register gagal'], 400);
        }
        $restaurant_id = $registered_data->id;
        $registered_data->bankAccount()->create($request->only(['bank_name', 'account_holder_name', 'account_number']));

        if ($request->hasFile('image')) {
            $imagePath = "id_$restaurant_id/restaurant_id_$restaurant_id.jpg";
            $imageController = app('App\Http\Controllers\ImageController');
            $streamedImage = $imageController->cropImage($request->file('image'));
            $imageLink = $imageController->uploadImage($streamedImage, $imagePath);
            $registered_data->update(['image' => $imageLink]);
        }

        try {
            $registered_data->sendEmailVerificationNotification();
        } catch (\Exception $e) {
            $registered_data->delete();
            return response()->json(['message' => 'Pengiriman Email Gagal'], 404);
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
            'message' => 'Register berhasil',
            'data' => new RegisterResource(['restaurant' => $registered_data->refresh()->load('bankAccount'), 'token' => $token])
        ], 201);
    }

    public function auth(Request $request) {
        return response()->json([
            'message' => 'Autentikasi berhasil',
            'data' => new RestaurantResource($request->user())
        ]);
    }

    public function logout(Request $request) {
        $deleted_token = $request->user()->currentAccessToken()->delete();
        if ($deleted_token) {
            return response()->json(['message' => 'Logout berhasil']);
        } else {
            return response()->json(['message' => 'Gagal menghapus token'], 400);
        }
    }

    public function verifyEmail($user_id, Request $request) {
        auth()->loginUsingId($user_id);
        $restaurant = $request->user();

        if (!$request->hasValidSignature()) {
            //redirect to failed verify page
            return redirect(env('FRONTEND_URL', 'https://www.google.com') . '/verifikasi-kadaluarsa');
        }

        if (!$restaurant->hasVerifiedEmail()) {
            $restaurant->markEmailAsVerified();
        }

        //redirect to success verify page
        return redirect(env('FRONTEND_URL', 'https://www.google.com') . '/verifikasi-sukses');
    }

    public function resendEmail(Request $request) {
        if ($request->user()->hasVerifiedEmail()) {
            return response()->json(['message' => 'Email sudah pernah diverifikasi'], 400);
        }

        $request->user()->sendEmailVerificationNotification();

        return response()->json(['message' => 'Link Email verifikasi telah berhasil dikirim ulang']);
    }

    public function sendResetPassword(Request $request) {
        Password::sendResetLink($request->validate(['email' => ['required', 'email', 'exists:App\Restaurant,email']]));
        return response()->json(['message' => 'Link reset password telah berhasil dikirim via Email']);
    }

    public function resetPassword(ResetPasswordRequest $request) {
        $email_password_status = Password::reset($request->validated(), function ($restaurant, $password) {
            $restaurant->password = Hash::make($password);
            $restaurant->save();
        });

        if ($email_password_status == Password::INVALID_TOKEN) {
            return response()->json(['message' => 'URL Password Reset tidak valid atau sudah kadaluarsa'], 404);
        }
        return response()->json(['message' => 'Reset password berhasil']);
    }
}
