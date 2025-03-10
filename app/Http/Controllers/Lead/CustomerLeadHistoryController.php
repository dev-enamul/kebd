<?php

namespace App\Http\Controllers\Lead;

use App\Http\Controllers\Controller;
use App\Models\SalesPipeline;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;

class CustomerLeadHistoryController extends Controller
{
    public function __invoke($user_id)
    {
        try {
            $lead_history = SalesPipeline::where('user_id', $user_id)
                ->with(['service', 'assignTo', 'followupCategory'])  
                ->get(); 
            $lead_history = $lead_history->map(function ($lead) {
                $employee = [
                    "id" =>$lead->assignTo->id??"-",
                    "name" =>$lead->assignTo->name??"-",
                    "email" =>$lead->assignTo->email??"-",
                    "profile_image" =>$lead->assignTo->profile_image??"-",
                ];
                return [
                    'id' => $lead->id, 
                    'status' => $lead->status,
                    'service' => @$lead->service->title ?? "-",
                    'employee' => $employee,
                    'followup_category' => @$lead->followupCategory->title ?? "-",
                ];
            }); 
            return success_response($lead_history);
        } catch (Exception $e) {
            return error_response($e->getMessage());
        }
        
    }

     
}
