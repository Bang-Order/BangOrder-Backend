<?php

namespace App\Http\Controllers;

use App\Http\Requests\Restaurant\ChangePasswordRequest;
use App\Http\Requests\Restaurant\RestaurantRequest;
use App\Http\Resources\Restaurant\DashboardIncomeResource;
use App\Http\Resources\Restaurant\DashboardWithdrawResource;
use App\Http\Resources\Restaurant\RestaurantResource;
use App\Restaurant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Intervention\Image\Facades\Image;

class RestaurantController extends Controller
{
    public function __construct() {
        $this->middleware(['auth:sanctum', 'verified'])->only('show', 'showDashboard', 'update', 'changePassword');
    }

    public function index()
    {
        return response()->json(['message' => 'Please specify the Restaurant ID'], 405);
    }

    public function store(Request $request)
    {
        return response()->json(['message' => 'There are no POST method in RestaurantController'], 405);
    }

    public function show(Request $request, Restaurant $restaurant)
    {
        if (Gate::denies('restaurant-auth', $restaurant)) {
            return response()->json(['message' => 'Anda tidak diizinkan untuk melakukan aksi ini'], 401);
        }
        return new RestaurantResource($restaurant->load('bankAccount'));
    }

    public function showDashboard(Request $request, Restaurant $restaurant) {
        if (Gate::denies('restaurant-auth', $restaurant)) {
            return response()->json(['message' => 'Anda tidak diizinkan untuk melakukan aksi ini'], 401);
        }

        $incomeData = $restaurant->balanceTransactions()
            ->selectRaw("DATE_FORMAT(created_at, '%d-%m-%Y') date, count(*) order_count, sum(amount) total_income")
            ->where('transaction_type', 'IN')
            ->groupBy('date');

        $thisMonthData = $restaurant->balanceTransactions()
            ->selectRaw("DATE_FORMAT(created_at, '%m-%Y') date, count(*) order_count, sum(amount) total_income")
            ->where('transaction_type', 'IN')
            ->groupBy('date')
            ->first();

        $withdrawData = $restaurant->balanceTransactions()
            ->where('transaction_type', 'OUT');

        if ($start_date = $request->start_date) {
            $request->validate([
                'start_date' => 'date_format:Y-m-d',
                'end_date' => 'date_format:Y-m-d'
            ]);
            $end_date = $request->end_date ?: now()->toDateString();
            if (strtotime($start_date) <= strtotime($end_date)) {
                $incomeData = $incomeData->whereBetween('created_at', [$start_date.' 00:00:00', $end_date.' 23:59:59']);
                $withdrawData = $withdrawData->whereBetween('created_at', [$start_date.' 00:00:00', $end_date.' 23:59:59']);
            }
            else {
                return response()->json(['message' => 'Tanggal mulai harus lebih rendah daripada tanggal akhir'], 422);
            }
        }

        if ($thisMonthData != null && $thisMonthData->date == date('m-Y')) {
            return response()->json([
                'total_balance' => $restaurant->bankAccount->total_balance,
                'this_month_data' => new DashboardIncomeResource($thisMonthData),
                'income_data' => DashboardIncomeResource::collection($incomeData->get()),
                'withdraw_data' => DashboardWithdrawResource::collection($withdrawData->get())
            ]);
        } else {
            return response()->json([
                'total_balance' => $restaurant->bankAccount->total_balance,
                'this_month_data' => [
                    'date' => date('m-Y'),
                    'total_order' => 0,
                    'total_income' => 0
                ],
                'income_data' => DashboardIncomeResource::collection($incomeData->get()),
                'withdraw_data' => DashboardWithdrawResource::collection($withdrawData->get())
            ]);
        }
    }

    public function update(RestaurantRequest $request, Restaurant $restaurant)
    {
        if ($request->hasFile('image')) {
            $imagePath = "id_$restaurant->id/restaurant_id_$restaurant->id.jpg";
            $imageController = app('App\Http\Controllers\ImageController');
            $streamedImage = $imageController->cropImage($request->file('image'));
            $imageLink = $imageController->uploadImage($streamedImage, $imagePath);
            $newrequest = $request->validated();
            $newrequest['image'] = $imageLink;
        } else {
            $newrequest = $request->validated();
        }

        $updatedData = $restaurant->update($newrequest)
            && $restaurant->bankAccount()->update($request->only(['bank_name', 'account_holder_name', 'account_number']));;

        if ($updatedData) {
            return response()->json([
                'message' => 'Data berhasil diupdate',
                'data' => new RestaurantResource($restaurant->load('bankAccount'))
            ]);
        } else {
            return response()->json(['message' => 'Gagal mengupdate data'], 400);
        }
    }

    public function destroy(Restaurant $restaurant)
    {
        return response()->json(['message' => 'There are no DELETE method in RestaurantController'], 405);
    }

    public function changePassword(Restaurant $restaurant, ChangePasswordRequest $request) {
        if (!Hash::check($request->old_password, $restaurant->password)) {
            return response()->json(['message' => 'Password lama tidak sesuai'], 422);
        }
        $restaurant->update(['password' => Hash::make($request->new_password)]);
        return response()->json(['message' => 'Sukses mengganti password']);
    }
}
