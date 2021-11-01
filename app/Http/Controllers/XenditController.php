<?php

namespace App\Http\Controllers;

use App\Order;
use Illuminate\Http\Request;

class XenditController extends Controller
{
    public function notify(Request $request) {
        if ($request->header('x-callback-token') === env('XENDIT_CALLBACK_TOKEN')) {
            $order = Order::where('transaction_id', $request->id)->firstOrFail();
            switch ($request->status) {
                case 'PAID':
                    $order->update(['order_status' => 'antri', 'invoice_url' => null]);
                    break;
                default:
                    $order->delete();
                    break;
            }
            return response();
        }
        return response($request->header('x-callback-token'), 401);
    }
}
