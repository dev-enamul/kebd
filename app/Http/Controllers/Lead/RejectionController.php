<?php

namespace App\Http\Controllers\Lead;

use App\Http\Controllers\Controller;
use App\Models\FollowupLog;
use App\Models\SalesPipeline;
use App\Models\User;
use App\Services\SalesPipelineService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RejectionController extends Controller
{
    protected $salesPipelineService;

    public function __construct(SalesPipelineService $salesPipelineService)
    {
        $this->salesPipelineService = $salesPipelineService;
    }
    
    public function index(Request $request)
    {
        try {
            if (!can("rejection")) {
                return permission_error_response();
            }  
            $status = $request->status ?? "Rejected";
            $authUser = User::find(Auth::user()->id);
 
            $query = $this->buildQuery($status);
            $datas = $this->filterByPermissions($query, $authUser); 
            $pagedData = $this->processAndPaginate($datas, $request);

            return success_response($pagedData);
        } catch (Exception $e) {
            return error_response($e->getMessage(), 500);
        }
    }

    private function buildQuery($status)
    { 
        $query = SalesPipeline::query()
            ->leftJoin('users', 'sales_pipelines.user_id', '=', 'users.id')
            ->leftJoin('sales_pipeline_services', 'sales_pipelines.id', '=', 'sales_pipeline_services.sales_pipeline_id')
            ->leftJoin('services', 'sales_pipeline_services.service_id', '=', 'services.id')
            ->select('sales_pipelines.id as lead_id', 'sales_pipelines.next_followup_date', 'sales_pipelines.last_contacted_at',
                    'users.id as user_id', 'users.name as user_name', 'users.email as user_email', 'users.phone as user_phone', 
                    'services.id as service_id', 'services.title as service_name')
            ->where('sales_pipelines.status', $status); 
        return $query;
    }

    private function filterByPermissions($query, $authUser)
    { 
        if (can('all-rejection')) {
            return $query->get();
        } elseif (can('own-team-rejection')) {
            $juniorUserIds = json_decode($authUser->junior_user ?? "[]");
            return $query->whereIn('sales_pipelines.assigned_to', $juniorUserIds)->get();
        } elseif (can('own-rejection')) {
            $directJuniors = $authUser->directJuniors->pluck('user_id')->toArray();
            return $query->whereIn('sales_pipelines.assigned_to', $directJuniors)->get();
        } else {
            return collect();
        }
    }

    private function processAndPaginate($datas, $request)
    { 
        $groupedData = $datas->groupBy('lead_id')->map(function ($salesPipelines) {
            $salesPipeline = $salesPipelines->first();
            $services = $salesPipelines->map(function ($pipeline) {
                return [
                    'id' => $pipeline->service_id,
                    'name' => $pipeline->service_name,
                ];
            });

            return [
                'id' => $salesPipeline->lead_id,
                'user_id' => $salesPipeline->user_id,
                'name' => $salesPipeline->user_name,
                'email' => $salesPipeline->user_email,
                'phone' => $salesPipeline->user_phone,
                'next_followup_date' => $salesPipeline->next_followup_date,
                'last_contacted_at' => $salesPipeline->last_contacted_at,
                'services' => $services,
            ];
        })->values(); 
        $sortedData = $groupedData->sortBy('next_followup_date'); 
        $perPage = $request->get('per_page', 20);
        $currentPage = $request->get('page', 1); 
        $pagedData = $sortedData->forPage($currentPage, $perPage);
    
        $totalItems = $sortedData->count();  
        $pagination = [
            'current_page' => $currentPage, 
            'total_items' => $totalItems,
            'per_page' => $perPage,
        ];

        return [
            'data' => $pagedData,
            'meta' => $pagination,
        ];
    }


    public function store(Request $request){ 
        if (!can("create-rejection")) {
            return permission_error_response();
        }  

        $lead = SalesPipeline::find($request->id);
        if($lead){
            $lead->status = "Rejected";
            $lead->save();
            return success_response(null, "Lead rejected successfully");
        } 
        return error_response(null, 404, 'Lead not found');
    }

    public function rejectToLead(Request $request){ 
        if (!can("create-lead")) {
            return permission_error_response();
        }    

        DB::beginTransaction(); 
        try {
            $pipeline = SalesPipeline::find($request->id); 
            if(!$pipeline){
                return error_response(null,404,"Lead not found");
            }
            $pipeline->update([
                'status' => "Active",
                'followup_categorie_id' => $request->followup_categorie_id,
                'purchase_probability' => $request->purchase_probability,
                'price' => $request->price,
                'next_followup_date' => $request->next_followup_date,
                'last_contacted_at' => now(),
            ]);

            // $this->createSalesPipelineService($pipeline, $request->service_ids ?? []);

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
