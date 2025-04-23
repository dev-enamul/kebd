<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Http\Requests\CustomerStoreRequest;
use App\Models\Customer;
use App\Models\FollowupCategory;
use App\Models\FollowupLog;
use App\Models\SalesPipeline;
use App\Models\User;
use App\Models\UserAddress;
use App\Models\UserContact;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            if (!can("lead")) {
                return permission_error_response();
            } 

            $category = $request->category_id??null;
            $status = $request->status ?? "Active";
            $authUser = User::find(Auth::user()->id);
 
            $query = $this->buildQuery($status, $category);
        
            $datas = $this->filterByPermissions($query, $authUser); 
            $pagedData = $this->processAndPaginate($datas, $request);

            return success_response($pagedData);
        } catch (Exception $e) {
            return error_response($e->getMessage(), 500);
        }
    }

    private function buildQuery($status, $category)
    { 
        $query = SalesPipeline::query()
            ->leftJoin('users', 'sales_pipelines.user_id', '=', 'users.id') 
            ->leftJoin('services', 'sales_pipelines.service_id', '=', 'services.id')
            ->leftJoin('followup_categories', 'sales_pipelines.followup_categorie_id', '=', 'followup_categories.id')
            ->select('sales_pipelines.id as lead_id', 'sales_pipelines.next_followup_date', 'sales_pipelines.last_contacted_at',
                    'users.id as user_id', 'users.project_name as project_name', 'users.client_name as client_name','users.profile_image', 'users.email as user_email', 'users.phone as user_phone', 
                    'services.id as service_id', 'services.title as service_name','followup_categories.title as lead_category')
            ->where('sales_pipelines.status', $status)
            ->where('sales_pipelines.type', 'customer_data');

        if (isset($category) && $category != null) {
            $query->where('sales_pipelines.followup_categorie_id', $category);
        }

        return $query;
    }

    private function filterByPermissions($query, $authUser)
    {
        if (can('all-lead')) {
            return $query->get()->toArray(); // Convert collection to array
        } elseif (can('own-team-lead')) {
            $juniorUserIds = json_decode($authUser->junior_user ?? "[]");
            return $query->whereIn('sales_pipelines.assigned_to', $juniorUserIds)->get()->toArray();
        } elseif (can('own-lead')) {
            $directJuniors = $authUser->directJuniors->pluck('user_id')->toArray();
            return $query->whereIn('sales_pipelines.assigned_to', $directJuniors)->get()->toArray();
        } else {
            return [];  
        }
    }
    


    private function processAndPaginate($datas, $request)
    { 
        $groupedData = collect($datas)->groupBy('lead_id')->map(function ($salesPipelines) {
            $salesPipeline = $salesPipelines->first();

            return [
                'id' => $salesPipeline['lead_id'] ?? null,
                'user_id' => $salesPipeline['user_id'] ?? null,
                'project_name' => $salesPipeline['project_name'] ?? null,
                'client_name' => $salesPipeline['client_name'] ?? null,
                'profile_image' => $salesPipeline['profile_image'] ?? null, // ✅ Now it's available
                'email' => $salesPipeline['user_email'] ?? null,
                'phone' => $salesPipeline['user_phone'] ?? null,
                'next_followup_date' => $salesPipeline['next_followup_date'] ?? null,
                'last_contacted_at' => $salesPipeline['last_contacted_at'] ?? null,
                'service' => $salesPipeline['service_name'] ?? null,
                'lead_category' => $salesPipeline['lead_category'] ?? null,
            ];
        })->values()->toArray();

        $sortedData = collect($groupedData)->sortBy('next_followup_date')->values()->toArray(); 

        $perPage = $request->get('per_page', 20);
        $currentPage = $request->get('page', 1);

        $pagedData = array_slice($sortedData, ($currentPage - 1) * $perPage, $perPage);

        return [
            'data' => array_values($pagedData),
            'meta' => [
                'current_page' => $currentPage,
                'total_items' => count($sortedData),
                'per_page' => $perPage,
            ],
        ];
    }



    public function leadReport(Request $request){
        try {
            $startDate = $request->start_date ?? Carbon::now()->startOfMonth()->toDateString();
            $endDate = $request->end_date ?? Carbon::now()->endOfMonth()->toDateString(); 
            $employee_id = $request->user_id ?? Auth::user()->id; 
     
            $logs = FollowupLog::where('created_by', $employee_id)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->paginate(10);  
    
            $logs = $logs->map(function ($log) {
                return [
                    "project_name"      => optional($log->user)->project_name ?? "-",
                    "followup_category" => optional($log->followupCategory)->title ?? "-",
                    "date"              => $log->created_at ?? "-",
                    "last_followup_date" => $log->updated_at ?? $log->created_at,
                    "notes"             => $log->notes ?? "-",
                ];
            });
    
            // Return the paginated data along with the total count
            return success_response([
                'total' => $logs->total(),   
                'current_page' => $logs->currentPage(),   
                'per_page' => $logs->perPage(),  
                'data' => $logs
            ]);
        } catch (Exception $e) {
            return error_response($e->getMessage());
        }
    }
  
}
