<?php

namespace App\Http\Controllers\Lead;

use App\Http\Controllers\Controller;
use App\Models\SalesPipeline;
use App\Services\SalesPipelineService;
use Illuminate\Http\Request;

class RejectionController extends Controller
{
    protected $salesPipelineService;

    public function __construct(SalesPipelineService $salesPipelineService)
    {
        $this->salesPipelineService = $salesPipelineService;
    }
    
    public function index(Request $request)
    {
        if (!can("lead")) {
            return permission_error_response();
        }
        
        try {
            $category_id = $request->get('category_id', null);
            $status = $request->get('status', 'Rejected');  
            $perPage = $request->get('per_page', 20);
            $currentPage = $request->get('page', 1);

            $authUser = auth()->user();
 
            $result = $this->salesPipelineService->getSalesPipelines($status, $category_id, $authUser, $perPage, $currentPage);

            return success_response($result);
        } catch (\Exception $e) {
            return error_response($e->getMessage(), 500);
        }
    } 

    public function store(Request $request){
        $lead = SalesPipeline::find($request->id);
        if($lead){
            $lead->status = "Rejected";
            $lead->save();
            return success_response(null, "Lead rejected successfully");
        } 
        return error_response(null, 404, 'Lead not found');
    }
    
}
