<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Models\Bank;
use App\Models\PaymentSchedule;
use App\Models\Salese;
use App\Models\Transaction;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    public function payNow(Request $request) {
        if (!can("payment-receive")) {
            return permission_error_response();
        } 
     
        $request->validate([
            'id' => 'required|exists:payment_schedules,id',
            'bank_id' => 'required|exists:banks,id',
        ]); 
        DB::beginTransaction();  
        try {
            $invoice = PaymentSchedule::findOrFail($request->id);
            if ($invoice->status == 1) {
                return error_response(null, 400, "This invoice has already been paid.");
            }
     
            $invoice->update([
                'status' => 1,
                'paid_amount' => $invoice->amount,
            ]);
     
            $sales = Salese::findOrFail($invoice->salese_id);
            $sales->increment('paid', $invoice->amount);
     
            Transaction::create([
                'bank_id' => $request->bank_id,
                'transaction_type' => 1, // 1 = Deposit
                'amount' => $invoice->amount,
                'transaction_date' => now(),
            ]);
     
            $bank = Bank::findOrFail($request->bank_id);
            $bank->increment('balance', $invoice->amount);
    
            DB::commit();  
            return success_response(null, "Payment processed successfully. The invoice is marked as paid.");
        
        } catch (Exception $e) {
            DB::rollBack(); 
            return error_response(null, 500, "An error occurred while processing the payment: " . $e->getMessage());
        }
    }
    
}
