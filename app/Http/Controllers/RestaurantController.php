<?php

namespace App\Http\Controllers;

use App\Http\Requests\Restaurant\RestaurantRequest;
use App\Http\Resources\Restaurant\DashboardIncomeResource;
use App\Http\Resources\Restaurant\DashboardWithdrawResource;
use App\Http\Resources\Restaurant\RestaurantResource;
use App\Restaurant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Gate;
use Intervention\Image\Facades\Image;

class RestaurantController extends Controller
{
    public function __construct() {
        $this->middleware('auth:sanctum')->only('show', 'showDashboard', 'update');
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
            return response()->json(['message' => 'This action is unauthorized.'], 401);
        }
        return new RestaurantResource($restaurant->load('bankAccount'));
    }

    public function showDashboard(Request $request, Restaurant $restaurant) {
        if (Gate::denies('restaurant-auth', $restaurant)) {
            return response()->json(['message' => 'This action is unauthorized.'], 401);
        }

        $incomeData = $restaurant->balanceTransactions()
            ->selectRaw("DATE_FORMAT(created_at, '%d-%m-%Y') date, count(*) order_count, sum(amount) total_income")
            ->where('transaction_type', 'IN')
            ->groupBy('date');

        $todayData = with(clone $incomeData)->first();

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
                return response()->json(['message' => 'Start date value must lower than end date'], 422);
            }
        }

        if ($todayData != null && $todayData->date == date('d-m-Y')) {
            return response()->json([
                'total_balance' => $restaurant->bankAccount->total_balance,
                'today_data' => new DashboardIncomeResource($todayData),
                'income_data' => DashboardIncomeResource::collection($incomeData->get()),
                'withdraw_data' => DashboardWithdrawResource::collection($withdrawData->get())
            ]);
        } else {
            return response()->json([
                'total_balance' => $restaurant->bankAccount->total_balance,
                'today_data' => [
                    'date' => date('d-m-Y'),
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
            $imageLink = $this->saveImage($restaurant->id, $request->file('image'));
            $newrequest = $request->validated();
            $newrequest['image'] = $imageLink;
        } else {
            $newrequest = $request->validated();
        }

        $updatedData = $restaurant->update($newrequest)
            && $restaurant->bankAccount()->update($request->only(['bank_name', 'account_holder_name', 'account_number']));;

        if ($updatedData) {
            return response()->json([
                'message' => 'Data successfully updated',
                'data' => new RestaurantResource($restaurant->load('bankAccount'))
            ]);
        } else {
            return response()->json(['message' => 'Update failed'], 400);
        }
    }

    public function destroy(Restaurant $restaurant)
    {
        return response()->json(['message' => 'There are no DELETE method in RestaurantController'], 405);
    }

    /**
     * @param string $restaurant_id
     * @param $image
     * @return string
     */
    public function saveImage(string $restaurant_id, $image): string
    {
        $imagePath = "id_$restaurant_id/restaurant_id_$restaurant_id.jpg";
        $imageLink = env('FIREBASE_STORAGE_URL') . str_replace('/', '%2F', $imagePath) . '?alt=media';

        list($width, $height) = getimagesize($image);
        if ($width != $height) {
            $size = $width < $height ? $width : $height;
            $encodedImage = Image::make($image)->fit($size)->stream('jpg');
        } else {
            $encodedImage = Image::make($image)->stream('jpg');
        }
        $bucket = app('firebase.storage')->getBucket();
        $bucket->upload($encodedImage->__toString(), [
            'name' => $imagePath
        ]);
        return $imageLink;
    }
}
