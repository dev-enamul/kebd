<?php

namespace App\Http\Controllers\Lead;

use App\Http\Controllers\Controller;
use App\Http\Requests\CustomerStoreRequest;
use App\Http\Requests\LeadStoreRequest;
use App\Models\Customer;
use App\Models\FollowupCategory;
use App\Models\FollowupLog;
use App\Models\Notification;
use App\Models\SalesPipeline;
use App\Models\SalesPipelineService;
use App\Models\User;
use App\Models\UserAddress;
use App\Models\UserContact;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class LeadController extends Controller
{ 
    public function index(Request $request)
    {
        try {
            if (!can("lead")) {
                return permission_error_response();
            } 

            $category = $request->category_id??null;
            $employeeId = $request->employee_id ?? null;
            $status = $request->status ?? "Active";
            $authUser = User::find(Auth::user()->id);
            $query = $this->buildQuery($status, $category);
        
            $datas = $this->filterByPermissions($query, $authUser,$employeeId);
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
            ->leftJoin('users as assigned_users', 'sales_pipelines.assigned_to', '=', 'assigned_users.id')
            ->leftJoin('services', 'sales_pipelines.service_id', '=', 'services.id')
            ->leftJoin('followup_categories', 'sales_pipelines.followup_categorie_id', '=', 'followup_categories.id')
            ->select(
                'sales_pipelines.id as lead_id',
                'sales_pipelines.next_followup_date',
                'sales_pipelines.last_contacted_at',
                'sales_pipelines.assigned_to',
                'assigned_users.name as assigned_to_name',

                'users.id as user_id',
                'users.project_name as project_name',
                'users.client_name as client_name',
                'users.profile_image',
                'users.email as user_email',
                'users.phone as user_phone',

                'services.id as service_id',
                'services.title as service_name',
                'followup_categories.title as lead_category'
            )
            ->where('sales_pipelines.status', $status)
            ->where('sales_pipelines.type', 'lead_data');

        if (!empty($category)) {
            $query->where('sales_pipelines.followup_categorie_id', $category);
        } 

        return $query;
    }



    private function filterByPermissions($query, $authUser, $employeeId=null)
    {
        if($employeeId!=null){
            return $query->where('sales_pipelines.assigned_to', $employeeId)->get()->toArray();
        }else{
             if (can('all-lead')) {
                return $query->get()->toArray(); 
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
                'assigned_to' => $salesPipeline['assigned_to_name'] ?? null,
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
    
    
    

    
    public function store(LeadStoreRequest $request)
    {
        if (!can("create-lead")) {
            return permission_error_response();
        } 
          
        $authUser = Auth::user(); 
        DB::beginTransaction();
        try {
            $profilePicPath = null;
            if ($request->hasFile('profile_image')) {
                $profilePicPath = $request->file('profile_image')->store('profile_images', 'public');
            }
            
            $user = User::create([
                'project_name'          => $request->project_name,
                'client_name'          => $request->client_name,
                'email'         => $request->client_office_email,
                'phone'         => $request->client_office_phone,
                'password'      => Hash::make("12345678"),

                'user_type'     => 'customer',  
                'profile_image' => $profilePicPath,  
                'dob'           => $request->dob, 
                'blood_group'   => $request->blood_group, 
                'gender'        => $request->gender, 
                'created_by'    => $authUser->id,
            ]);

            $customer = Customer::create([ 
                'user_id' => $user->id,
                'lead_source_id'    => $request->lead_source_id,
                'referred_by'       => $request->referred_by,  
                'created_by' => $authUser->id,
            ]);

            $followup_category = FollowupCategory::orderBy('serial', 'asc')->first(); 
            $pipeline = SalesPipeline::create([
                'user_id'           => $user->id,
                'customer_id'       => $customer->id,
                'service_id'        => $request->service_id,
                'service_details'   => $request->service_details,
                'qty'               => $request->qty,
                'followup_categorie_id' => $followup_category->id,
                'assigned_to'       => $authUser->id,
                'type'              => "lead_data",
            ]);

            // if(isset($request->service_ids) && count($request->service_ids)>0){
            //     foreach($request->service_ids as $service_id){
            //         SalesPipelineService::create([
            //             'user_id' =>  $user->id,
            //             'customer_id' => $customer->id,
            //             'sales_pipeline_id' => $pipeline->id,
            //             'service_id' => $service_id,
            //         ]);
            //     }
            // }  

            $leadCategory = FollowupCategory::where('status',1)->first();
            FollowupLog::create([
                'user_id' => $user->id,
                'followup_categorie_id' => $leadCategory->id,
                'customer_id' => $customer->id,
                'pipeline_id' => $pipeline->id,
                'followup_category_id' => $followup_category->id,
                'notes' => $request->notes,
                'created_by' => Auth::user()->id
            ]);
 
            UserContact::create([
                'user_id'       => $user->id,
                'name'          => $request->client_name,
                'role'          => "Client Office",
                'phone'         => $request->client_office_phone, 
                'email'         => $request->office_email, 
                'address'       => $request->client_office_email,  

                'whatsapp'      => $request->whatsapp,
                'imo'           => $request->imo,
                'facebook'      => $request->facebook,
                'linkedin'      => $request->linkedin,
            ]);

            if($request->site_person!=null){
                UserContact::create([
                    'user_id'       => $user->id,
                    'name'          => $request->site_person,
                    'role'          => "Site",
                    'phone'         => $request->site_phone,
                    'email'         => $request->site_email, 
                    'address'       => $request->site_address,
    
                    'whatsapp'      => $request->whatsapp,
                    'imo'           => $request->imo,
                    'facebook'      => $request->facebook,
                    'linkedin'      => $request->linkedin,
                ]);
            }
            
 
            // UserAddress::create([
            //     'user_id' => $user->id,
            //     'address_type'      => "permanent",
            //     'country'           => $request->permanent_country,
            //     'division'          => $request->permanent_division,
            //     'district'          => $request->permanent_district,
            //     'upazila_or_thana'  => $request->permanent_upazila_or_thana,
            //     "zip_code"          => $request->permanent_zip_code,
            //     'address'           => $request->permanent_address, 
            //     "is_same_present_permanent" => $request->is_same_present_permanent
            // ]);

            // if(!$request->is_same_present_permanent){
            //     UserAddress::create([
            //         'user_id' => $user->id,
            //         'address_type'      => "present",
            //         'country'           => $request->present_country,
            //         'division'          => $request->present_division,
            //         'district'          => $request->present_district,
            //         'upazila_or_thana'  => $request->present_upazila_or_thana,
            //         "zip_code"          => $request->present_zip_code,
            //         'address'           => $request->present_address, 
            //         "is_same_present_permanent" => $request->is_same_present_permanent
            //     ]);
            // } 
            

            // if($authUser != $request->assigned_to){
            //     Notification::create([
            //         'user_id' => $request->assigned_to,
            //         'title' => "New Lead Assigned!",
            //         'data' => "A new lead has been assigned to you. Take action now and convert it into a successful deal!",
            //     ]);  
            // }
            DB::commit();  
            return success_response(null,'Lead has been created');

        } catch (\Exception $e) { 
            DB::rollBack();  
            return error_response($e->getMessage(), 500);
        }
    } 

    public function profile($id)
    {
        if (!can("lead") && !can("client") ) {
            return permission_error_response();
        } 

        try {  
            $lead = SalesPipeline::find($id);  
            if (!$lead) {
                return error_response('Lead not found', 404);
            }
  
            $followup = $lead->followup;
 
            $user = User::find($lead->user_id); 
            if (!$user) {
                return error_response('User not found', 404);
            } 
 
            return success_response([ 
                'user_id' => $user->id, 
                "name" => $user->name,
                "project_name" => $user->project_name,
                "client_name" => $user->client_name,
                'email' => $user->email,
                'phone' => $user->phone,
                'profile_image' => $user->profile_image,
                "dob" => $user->dob, 
                'blood_group' => $user->blood_group,
                'gender' => $user->gender,  
                'followup' => $followup,
            ]);
        } catch (Exception $e) {
            return error_response($e->getMessage(), 500);
        }
    }


}
