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
            $lead_history = SalesPipeline::where('user_id', $user_id)->get();

            $lead_history = $lead_history->map(function ($lead) {
                return [
                    'status' => $lead->status,   
                    'service' => optional($lead->services)->title,
                    'employee' => optional($lead->assignTo)->name,
                    'followup_category' => optional($lead->followupCategory)->title,
                ];
            });

            return success_response($lead_history);
        } catch (Exception $e) {
            return error_response($e->getMessage());
        }
    }

     
}
