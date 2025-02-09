<?php

namespace App\Http\Controllers\Salese;

use App\Http\Controllers\Controller;
use App\Http\Requests\Salse\SalseStoreRequest;
use App\Models\PaymentSchedule;
use App\Models\Salese;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SaleseController extends Controller
{
    public function index(){
        try {
            $sales_by_user_id = Auth::user()->id; 
            $datas = Salese::with(['user', 'salesByUser']) 
                ->where('sales_by_user_id', $sales_by_user_id)
                ->get();
         
            $result = $datas->map(function($sale) {
                return [
                    'user_id' => $sale->user_id,
                    'user_name' => $sale->user->name, 
                    'sales_by_user_id' => $sale->sales_by_user_id,
                    'sales_by_user_name' => $sale->salesByUser->name,
                    'price' => $sale->price,
                    'paid' => $sale->paid,
                    'payment_schedule_amount' => $sale->payment_schedule_amount,
                    'is_paid' => $sale->is_paid,
                    'is_deliveried' => $sale->is_deliveried,
                ];
            });
        
            return success_response($result);
        
        } catch (Exception $e) {
            return error_response($e->getMessage());
        }
        
    }

    public function store(SalseStoreRequest $request)
    {  

        try{
            $sales_by_user_id = Auth::user()->id; 

            $salese = Salese::create([
                'user_id' => $request->user_id,
                'sales_pipeline_id' => $request->lead_id,  
                'sales_by_user_id' => $sales_by_user_id,
                'price' => $request->price,
                'payment_schedule_amount' => $request->payment_schedule_amount
            ]);
         
            $payment_schedule = $request->payment_schedule;
        
            $total_schedule_amount = 0;
            if (isset($payment_schedule) && count($payment_schedule) > 0) { 

                foreach ($payment_schedule as $schedule) {
                    $total_schedule_amount += $schedule['amount'];
                } 
                if ($total_schedule_amount > $salese->price) {
                    return error_response(null,404,"The total payment schedule amount cannot exceed the sale price.");
                }

                foreach ($payment_schedule as $schedule) {
                    PaymentSchedule::create([
                        'user_id' => $request->user_id,
                        'salese_id' => $salese->id,  
                        'date' => $schedule['date'],  
                        'amount' => $schedule['amount'],
                        'paid_amount' => 0,  
                    ]);
                }
            }
            return success_response(null, "Salese created successfully");
        }catch(Exception $e){
            return error_response($e->getMessage());
        }
    } 
 

    public function show(){

    } 


    public function delet3e(){

    }
}
