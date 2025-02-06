<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Models\PaymentSchedule;
use App\Models\Salese;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentScheduleController extends Controller
{

    public function index(Request $request){ 

        $request->validate([
            'login_user_id' => 'required|integer'
        ]);  
        try{
            $login_user_id = $request->login_user_id; 
            $datas = PaymentSchedule::whereHas('sale',function($q) use ($login_user_id){
                $q->where('sales_by_user_id',$login_user_id);
            })->get(); 

            $result = $datas->map(function($data) {
                return [
                    'id' => $data->id,
                    'user_id' => $data->user_id,
                    'user_name' => $data->user->name, 
                    'sales_by_user_id' => $data->sale->sales_by_user_id??"",
                    'sales_by_user_name' => $data->sale->salesByUser->name??"",
                    'date' => $data->date,
                    'amount' => $data->amount,
                    'status' => $data->status,
                ];
            });
        
            return success_response($result); 
        }catch(Exception $e){
            return error_response($e->getMessage());
        }
    }
    
    
    public function store(Request $request){
        $request->validate([
            'salese_id' => "required",
            'payment_schedule' => 'nullable|array',
            'payment_schedule.*.date' => 'required|date',
            'payment_schedule.*.amount' => 'required|numeric',
        ]);
     
        DB::beginTransaction();
    
        try { 
            $salese = Salese::findOrFail($request->salese_id); 
            $total_schedule_amount = 0;
            $payment_schedule = $request->payment_schedule; 
            if (isset($payment_schedule) && count($payment_schedule) > 0) {  
                foreach ($payment_schedule as $schedule) {
                    $total_schedule_amount += $schedule['amount'];
                }  
                if (($total_schedule_amount + $salese->payment_schedule_amount) > $salese->price) {
                    return error_response(null,404,"The total payment schedule amount cannot exceed the sale price.");
                }
     
                $salese->payment_schedule_amount += $total_schedule_amount;
                $salese->save();
     
                foreach ($payment_schedule as $schedule) {
                    PaymentSchedule::create([
                        'user_id' => $salese->user_id,
                        'salese_id' => $salese->id,  
                        'date' => $schedule['date'],  
                        'amount' => $schedule['amount'],
                        'paid_amount' => 0,  
                    ]);
                }
            }
     
            DB::commit();
            return success_response(null,"Payment schedules created successfully.");
        } catch (\Exception $e) { 
            DB::rollBack(); 
            return error_response($e->getMessage()); 
        }
    }

    public function show($id)
    {
        try { 
            $data = PaymentSchedule::findOrFail($id); 
            $data->load('sale.salesPipeline.services'); 
     
            $response = [
                'id' => $data->id,
                'user_id' => $data->user_id,
                'user_name' => $data->user->name,
                'sales_by_user_id' => $data->sale->sales_by_user_id ?? "",
                'sales_by_user_name' => $data->sale->salesByUser->name ?? "",
                'services' => $data->sale->services ? $data->sale->salesPipeline->services->pluck('title') : [],
                'date' => $data->date,
                'amount' => $data->amount,
                'status' => $data->status,
            ];
     
            return success_response($response);
    
        } catch (\Exception $e) { 
            return error_response($e->getMessage());
        }
    }
    
    
}
