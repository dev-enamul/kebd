<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Models\PaymentSchedule;
use App\Models\Salese;
use Exception;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function payNow(Request $request) {
        if (!can("payment-receive")) {
            return permission_error_response();
        } 
        try {
            $invoice = PaymentSchedule::find($request->id);
            $invoice->status = 1;
            $invoice->paid_amount = $invoice->amount;
            $invoice->save();
    
            $sales = Salese::find($invoice->salese_id);
            $sales->paid = $sales->paid + $invoice->amount;
            $sales->save();
    
            return success_response(null, "Payment has been processed successfully. The invoice is marked as paid.");
        } catch (Exception $e) {
            return error_response("An error occurred while processing the payment: " . $e->getMessage());
        }
    }
    
}
