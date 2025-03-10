<?php

namespace App\Http\Controllers\Lead;

use App\Http\Controllers\Controller;
use App\Models\SalesPipeline;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;

class CustomerLeadHistoryController extends Controller
{
    public function __invoke($id)
    {
        try{
            $lead_history = SalesPipeline::where('user_id',$id)->get();
            $lead_history->map(function($lead){
                return[
                    'status' => $lead,
                    'service' => $lead->services->title,
                    'employee' =>$lead->assignTo,
                    'followup_category' => $lead->followupCategory->title,
                ];
            });
            return success_response($lead_history);
        }catch(Exception $e){
            return error_response($e->getMessage());
        }
    } 

     
}
