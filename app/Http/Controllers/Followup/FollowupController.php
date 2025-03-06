<?php

namespace App\Http\Controllers\Followup;

use App\Http\Controllers\Controller;
use App\Http\Requests\FollowupRequest;
use App\Models\FollowupLog;
use App\Models\SalesPipeline;
use App\Models\SalesPipelineService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FollowupController extends Controller
{
    public function index(Request $request)
    {
        if (!can("lead")) {
            return permission_error_response();
        }  

        try {
            $lead_id = $request->lead_id;
            $customer_id = $request->customer_id;
            $user_id = $request->user_id; 
            $query = FollowupLog::query();

            if ($lead_id) {
                $query->where('pipeline_id', $lead_id);
            }

            if ($customer_id) {
                $query->where('customer_id', $customer_id);
            }

            if ($user_id) {
                $query->where('user_id', $user_id);
            }

            // Eager load followupCategory relationship
            $datas = $query->with('followupCategory')
                        ->get()
                        ->map(function ($followup) {
                            return [
                                'id' => $followup->id,
                                'followup_category' => $followup->followupCategory->title ?? "", 
                                'next_followup_date' => $followup->next_followup_date,
                                'date' => $followup->created_at,
                                'followup_by' => $followup->user->name??"-",
                                'lead_category' => $followup->followupCategory->title??"-",
                                'notes' => $followup->notes, 
                            ];
                        });

            return success_response($datas, 200);
        } catch (Exception $e) {
            return error_response($e->getMessage(), 500);
        }
    }



    public function store(FollowupRequest $request){  
        if (!can("create-lead")) {
            return permission_error_response();
        }  

        DB::beginTransaction(); 
        try {
            $pipeline = SalesPipeline::findOrFail($request->lead_id);

            $pipeline->update([
                'followup_categorie_id' => $request->followup_categorie_id, 
                'price' => $request->price,
                'next_followup_date' => $request->next_followup_date,
                'last_contacted_at' => now(),
            ]); 

            FollowupLog::create([
                'user_id' => $pipeline->user_id,
                'customer_id' => $pipeline->customer_id,
                'pipeline_id' => $pipeline->id,
                'followup_categorie_id' => $request->followup_categorie_id,
                'purchase_probability' => $request->purchase_probability,
                'price' => $request->price,
                'next_followup_date' => $request->next_followup_date,
                'notes' => $request->notes,
                'created_by' => Auth::user()->id
            ]); 
            DB::commit(); 
            return success_response("Follow-up created successfully", 200);
        } catch (Exception $e) {
            DB::rollBack();
            return error_response($e->getMessage(), 500);
        } 
    } 
    
}
