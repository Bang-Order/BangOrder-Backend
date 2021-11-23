<?php

namespace App\Http\Controllers;

use App\Order;
use Illuminate\Http\Request;

class XenditController extends Controller
{
    public function notify(Request $request) {
        if ($request->header('x-callback-token') === env('XENDIT_CALLBACK_TOKEN')) {
            $order = Order::where('transaction_id', $request->id)->firstOrFail();
            $database = app('firebase.database');
            $referencePath = "orders/$order->restaurant_id/$order->id";
            switch ($request->status) {
                case 'PAID':
                    $order->update(['order_status' => 'antri', 'invoice_url' => null]);
                    if ($database->getReference($referencePath)->getSnapshot()->exists()) {
                        $database
                            ->getReference($referencePath)
                            ->getChild('order_status')
                            ->set('antri');
                    }
                    return response('Success: order has been paid', 200);
                default:
                    $order->delete();
                    if ($database->getReference($referencePath)->getSnapshot()->exists()) {
                        $database
                            ->getReference($referencePath)
                            ->remove();
                    }
                    return response('Timeout: Order has been expired and deleted', 200);
            }
        }
        return response('Failed: Token is invalid', 401);
    }
}
