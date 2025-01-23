<?php

namespace App\Http\Controllers\Lead;

use App\Http\Controllers\Controller;
use App\Http\Requests\CustomerStoreRequest;
use App\Models\Customer;
use App\Models\FollowupCategory;
use App\Models\FollowupLog;
use App\Models\SalesPipeline;
use App\Models\SalesPipelineService;
use App\Models\User;
use App\Models\UserAddress;
use App\Models\UserContact;
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
            
            $category = $request->category_id;
            $status = $request->status ?? "Active";
            
            $query = SalesPipeline::query()
                ->leftJoin('users', 'sales_pipelines.user_id', '=', 'users.id')
                ->leftJoin('sales_pipeline_services', 'sales_pipelines.id', '=', 'sales_pipeline_services.sales_pipeline_id')
                ->leftJoin('services', 'sales_pipeline_services.service_id', '=', 'services.id')
                ->select('sales_pipelines.id as lead_id', 'sales_pipelines.next_followup_date', 'sales_pipelines.last_contacted_at', 
                         'users.name as user_name', 'users.email as user_email', 'users.phone as user_phone', 
                         'services.id as service_id', 'services.title as service_name')
                ->where('sales_pipelines.status', $status);
        
            if ($category) {
                $query->where('sales_pipelines.followup_categorie_id', $category);
            }  

            $user = User::find(Auth::user()->id);
            $designation = @$user->employee->designation->slug;
            if($designation!="admin"){
                $query->where('sales_pipelines.assigned_to', $user->id);
            }

        
            $datas = $query->get();
        
            // Grouping the data by `lead_id` to ensure only one row per SalesPipeline
            $datas = $datas->groupBy('lead_id')->map(function ($salesPipelines) {
                $salesPipeline = $salesPipelines->first();  // Get the first row (as all rows have the same lead_id)
                
                // Group the services related to the salesPipeline
                $services = $salesPipelines->map(function ($pipeline) {
                    return [
                        'id' => $pipeline->service_id,
                        'name' => $pipeline->service_name,
                    ];
                });
        
                return [
                    'id' => $salesPipeline->lead_id,
                    'name' => $salesPipeline->user_name,
                    'email' => $salesPipeline->user_email,
                    'phone' => $salesPipeline->user_phone,
                    'next_followup_date' => $salesPipeline->next_followup_date,
                    'last_contacted_at' => $salesPipeline->last_contacted_at,
                    'services' => $services,
                ];
            });
        
            return success_response($datas);
        } catch (Exception $e) {
            return error_response($e->getMessage(), 500);
        }
        
        
        
    }
    
    public function store(CustomerStoreRequest $request)
    { 
        DB::beginTransaction();
        try {
            $profilePicPath = null;
            if ($request->hasFile('profile_image')) {
                $profilePicPath = $request->file('profile_image')->store('profile_images', 'public');
            }
  
            $user = User::create([
                'name'          => $request->name,
                'email'         => $request->email,
                'phone'         => $request->phone,
                'password'      => Hash::make("12345678"),
                'user_type'     => 'customer',  
                'profile_image' => $profilePicPath,  
                'dob'           => $request->dob, 
                'blood_group'   => $request->blood_group, 
                'gender'        => $request->gender, 
                'created_by'    => Auth::user()->id,
            ]); 

            $customer = Customer::create([ 
                'user_id' => $user->id,
                'lead_source_id'    => $request->lead_source_id,
                'referred_by'       => $request->referred_by,  
                'created_by' => Auth::user()->id,
            ]);

            $followup_category = FollowupCategory::orderBy('serial', 'asc')->first(); 
            $pipeline = SalesPipeline::create([
                'user_id' =>  $user->id,
                'customer_id' => $customer->id,
                'service_ids' => json_encode($request->service_ids),
                'followup_categorie_id' => $followup_category->id,
                'assigned_to' => $request->assigned_to,
            ]);

            if(isset($request->service_ids) && count($request->service_ids)>0){
                foreach($request->service_ids as $service_id){
                    SalesPipelineService::create([
                        'user_id' =>  $user->id,
                        'customer_id' => $customer->id,
                        'sales_pipeline_id' => $pipeline->id,
                        'service_id' => $service_id,
                    ]);
                }
            }

            FollowupLog::create([
                'user_id' => $user->id,
                'customer_id' => $customer->id,
                'pipeline_id' => $pipeline->id,
                'followup_category_id' => $followup_category->id,
                'notes' => $request->notes,
            ]);
 
            UserContact::create([
                'user_id'           => $user->id,
                'name'              => $request->name,
                'relationship_or_role' => "Employee",
                'office_phone'      => $request->office_phone,
                'personal_phone'    => $request->personal_phone,
                'office_email'      => $request->office_email,
                'personal_email'    => $request->personal_email,
                'whatsapp'          => $request->whatsapp,
                'imo'               => $request->imo,
                'facebook'          => $request->facebook,
                'linkedin'          => $request->linkedin,
            ]);
 
            UserAddress::create([
                'user_id' => $user->id,
                'address_type'      => "permanent",
                'country'           => $request->permanent_country,
                'division'          => $request->permanent_division,
                'district'          => $request->permanent_district,
                'upazila_or_thana'  => $request->permanent_upazila_or_thana,
                "zip_code"          => $request->permanent_zip_code,
                'address'           => $request->permanent_address, 
                "is_same_present_permanent" => $request->is_same_present_permanent
            ]);

            if(!$request->is_same_present_permanent){
                UserAddress::create([
                    'user_id' => $user->id,
                    'address_type'      => "present",
                    'country'           => $request->present_country,
                    'division'          => $request->present_division,
                    'district'          => $request->present_district,
                    'upazila_or_thana'  => $request->present_upazila_or_thana,
                    "zip_code"          => $request->present_zip_code,
                    'address'           => $request->present_address, 
                    "is_same_present_permanent" => $request->is_same_present_permanent
                ]);
            } 
            
            DB::commit();  
            return success_response(null,'Lead has been created');

        } catch (\Exception $e) { 
            DB::rollBack();  
            return error_response($e->getMessage(), 500);
        }
    }
}
