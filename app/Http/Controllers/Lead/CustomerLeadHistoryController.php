<?php

namespace App\Http\Controllers\Lead;

use App\Http\Controllers\Controller;
use App\Models\FollowupLog;
use App\Models\SalesPipeline;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
                    "profile_image" =>$lead->assignTo->profile_image,
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

    public function leadReport(Request $request)
    {
        try { 
            $perPage = $request->get('per_page', 20);   
            $currentPage = $request->get('page', 1);   
    
            $startDate = $request->start_date ?? Carbon::now()->startOfMonth()->toDateString();
            $endDate = $request->end_date ?? Carbon::now()->endOfMonth()->toDateString(); 
            $employee_id = $request->user_id ?? Auth::user()->id; 
     
            $logs = FollowupLog::where('created_by', $employee_id)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->paginate($perPage, ['*'], 'page', $currentPage);  
     
            $logs = $logs->map(function ($log) {
                return [
                    "project_name"      => optional($log->user)->project_name ?? "-",
                    "followup_category" => optional($log->followupCategory)->title ?? "-",
                    "date"              => $log->created_at ?? "-",
                    "notes"             => $log->notes ?? "-",
                ];
            });
    
            return success_response([
                'data' => $logs,
                'pagination' => [
                    'total' => $logs->total(),
                    'per_page' => $logs->perPage(),
                    'current_page' => $logs->currentPage(),
                    'last_page' => $logs->lastPage(),
                    'next_page_url' => $logs->nextPageUrl(),
                    'prev_page_url' => $logs->previousPageUrl(),
                ]
            ]);
        } catch (Exception $e) {
            return error_response($e->getMessage());
        }
    }
    

     
}
