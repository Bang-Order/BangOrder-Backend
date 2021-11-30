<?php

namespace App\Http\Controllers;

use App\BankAccount;
use App\Order;
use App\Restaurant;
use Illuminate\Http\Request;

class XenditController extends Controller
{
    public function __construct()
    {
        $this->middleware('xendit-verify');
    }

    public function orderNotify(Request $request) {
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
                $order->restaurant->balanceTransactions()->create([
                    'transaction_type' => 'IN',
                    'amount' => $request->paid_amount
                ]);
                $bankAccount = $order->restaurant->bankAccount;
                $bankAccount->update(['total_balance' => $bankAccount->total_balance + $request->paid_amount]);
                return response()->json(['message' => 'Success: Order has been paid']);
            default:
                $order->delete();
                if ($database->getReference($referencePath)->getSnapshot()->exists()) {
                    $database
                        ->getReference($referencePath)
                        ->remove();
                }
                return response()->json(['message' => 'Timeout: Order has been expired and deleted'], 408);
        }
    }

    public function withdrawNotify(Request $request) {
        switch ($request->status) {
            case 'COMPLETED':
                $amount = $request->amount + BankAccount::ADMIN_CHARGE;
                $external_id = explode('/', $request->external_id, 2);
                $restaurant = Restaurant::findOrFail($external_id[0]);
                $restaurant->balanceTransactions()->create([
                    'transaction_type' => 'OUT',
                    'amount' => $amount
                ]);
                $bankAccount = $restaurant->bankAccount;
                $bankAccount->update(['total_balance' => $bankAccount->total_balance - $amount]);
                return response()->json(['message' => 'Success: Withdrawal has been successfully transfered']);
            default:
                return response()->json([
                    'message' =>  'Failed: Withdrawal process is failed',
                    'error_code' => $request->failure_code
                ], 400);
        }
    }
}
