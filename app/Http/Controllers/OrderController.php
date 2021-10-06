<?php

namespace App\Http\Controllers;

use App\Http\Resources\Order\OrderCollection;
use App\Order;
use App\Restaurant;
use App\RestaurantTable;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Order $order, Request $request)
    {
        $query = $order->where('restaurant_id', $request->restaurant)->get();
        return new OrderCollection($query);
    }

    public function store(Request $request)
    {
        //
    }

    public function show(Order $order)
    {
        //
    }

    public function update(Request $request, Order $order)
    {
        //
    }

    public function destroy(Order $order)
    {
        //
    }
}
