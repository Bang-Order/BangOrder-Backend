<?php

namespace App\Http\Controllers;

use App\BankAccount;
use App\Http\Requests\BankAccount\WithdrawRequest;
use App\Restaurant;
use Exception;
use Illuminate\Http\Request;
use Xendit\Disbursements;
use Xendit\Xendit;

class BankAccountController extends Controller
{
    public function __construct() {
        $this->middleware(['auth:sanctum', 'verified']);
    }

    public function withdraw(WithdrawRequest $request, Restaurant $restaurant) {
        $bankAccount = $restaurant->bankAccount;
        if ($bankAccount->total_balance + BankAccount::ADMIN_CHARGE < $request->amount) {
            return response()->json([
                'message' => 'The given data was invalid.',
                'errors' => [
                    'amount' => 'Jumlah dana yang ingin ditarik melebihi saldo Anda'
                ]],422);
        }

        // Create xendit disbursement
        try {
            Xendit::setApiKey(env('XENDIT_API_KEY'));
            $params = [
                'external_id' => $restaurant->id . '/' . now()->format('d-m-Y/H:i:s'),
                'amount' => (int)$request->amount,
                'bank_code' => $bankAccount->bank_name,
                'account_holder_name' => $bankAccount->account_holder_name,
                'account_number' => $bankAccount->account_number,
                'description' => "Penarikan dana oleh $restaurant->name menuju rekening a.n $bankAccount->account_holder_name via Bang Order",
                'email_to' => [$restaurant->email],
            ];
            $createDisbursement = Disbursements::create($params);
            return response()->json([
                'message' => 'Created Disbursement Withdraw Request Successfully',
                'disbursement_data' => $createDisbursement
            ]);
        } catch (Exception $e) {
            return response()->json(['message' => 'Xendit error: ' . $e->getMessage(), 'error_code' => $e->getCode()], 400);
        }
    }
}
