<?php

namespace App\Http\Controllers\Lead;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\SalesPipeline;
use Exception;
use Illuminate\Http\Request;

class LeadAssignController extends Controller
{
    public function __invoke(Request $request)
    {
        try{
            $lead_id = $request->lead_id;  
            $lead = SalesPipeline::find($lead_id);
            $lead->assigned_to = $request->assign_to;
            $lead->save();  

            Notification::create([
                'user_id' => $request->assigned_to,
                'title' => "New Lead Assigned!",
                'data' => "A new lead has been assigned to you. Take action now and convert it into a successful deal!",
            ]);  

            return success_response("Lead assigned successfully");
        }catch(Exception $e){
            return error_response($e->getMessage());
        }

    }
}
