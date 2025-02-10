<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Models\PaymentSchedule;
use App\Models\Salese;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PaymentScheduleController extends Controller
{

    public function index(Request $request){  
        try{
            if (!can("payment-schedule")) {
                return permission_error_response();
            } 
            // if (!can('all-payment-schedule') && !can('own-payment-schedule') && !can('own-team-payment-schedule')) { 
            //     return success_response([]);
            // }

            if (!Auth::check()) {
                return error_response('User not authenticated.');
            }

            $status = $request->status; 
            $datas = PaymentSchedule::whereHas('sale',function($q){
                $authUser = User::find(Auth::user()->id);  
                if(can('own-team-payment-schedule')){
                    $juniorUserIds = json_decode($authUser->junior_user ?? "[]");
                    $q->whereIn('sales_by_user_id',$juniorUserIds); 
                }elseif(can('own-payment-schedule')){
                    $directJuniors = $authUser->directJuniors->pluck('user_id')->toArray();
                    $q->whereIn('sales_by_user_id',$directJuniors);  
                } 
                
            })
            ->when(isset($status),function($q) use ($status){
                $q->where('status',$status);
            })
            ->get(); 

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
        if (!can("create-payment-schedule")) {
            return permission_error_response();
        } 
        $request->validate([
            'salese_id' => "required",
            'payment_schedule' => 'nullable|array',
            'payment_schedule.*.date' => 'required|date',
            'payment_schedule.*.amount' => 'required|numeric',
        ]);
     
        DB::beginTransaction();
    
        try { 
            $salese = Salese::find($request->salese_id); 
            if(!$salese){
                return error_response(null,404, "Sales not found");
            }
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
        if (!can("payment-schedule")) {
            return permission_error_response();
        } 
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
