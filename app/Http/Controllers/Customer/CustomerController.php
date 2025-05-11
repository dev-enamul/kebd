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
        if (!can("customer")) {
            return permission_error_response();
        }

        $keyword = $request->keyword;
        $authUser = Auth::user();

        $datas = User::where('user_type', "customer")
            ->with(['contacts']);

        if (can('all-customer')) {
            // No additional filter
        } elseif (can('own-team-customer')) {
            $juniorUserIds = json_decode($authUser->junior_user ?? "[]");
            $datas = $datas->whereIn('created_by', $juniorUserIds);
        } elseif (can('own-customer')) {
            $directJuniors = $authUser->directJuniors->pluck('user_id')->toArray();
            $datas = $datas->whereIn('created_by', $directJuniors);
        } else {
            return success_response([]);
        }

        if ($keyword) {
            $datas = $datas->where(function ($q) use ($keyword) {
                $q->where('project_name', 'like', "%{$keyword}%")
                ->orWhere('client_name', 'like', "%{$keyword}%")
                ->orWhere('name', 'like', "%{$keyword}%")
                ->orWhereHas('contacts', function ($contactQuery) use ($keyword) {
                    $contactQuery->where('role', 'like', "%{$keyword}%")
                                ->orWhere('name', 'like', "%{$keyword}%");
                });
            });
        }

        $datas = $datas->get();

        $contacts = collect();

        foreach ($datas as $user) {
            foreach ($user->contacts as $contact) {
                $contacts->push([
                    "id" => $user->id,
                    "project_name" => $user->project_name,
                    "client_name" => $user->client_name,
                    "contact_person_name" => $contact->name ?? '',
                    "contact_person_designation" => $contact->role ?? '',
                ]);
            }
        }

        $sortedData = $contacts->sortBy('project_name')->values();

        $perPage = (int) $request->get('per_page', 10);
        $currentPage = (int) $request->get('page', 1);
        $paginatedData = $sortedData->forPage($currentPage, $perPage)->values();

        return success_response([
            'data' => $paginatedData,
            'meta' => [
                'current_page' => $currentPage,
                'total_items' => $sortedData->count(),
                'per_page' => $perPage,
            ],
        ]);
    }
 
 
 
 

    public function store(Request $request)
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

            // Create user
            $user = User::create([
                'project_name'  => $request->project_name,
                'client_name'   => $request->client_name,
                'name'         => $request->contacts[0]['name'] ?? null,
                'email'         => $request->contacts[0]['email'] ?? null,
                'phone'         => $request->contacts[0]['phone'] ?? null,
                'password'      => Hash::make("12345678"),
                'user_type'     => 'customer',
                'profile_image' => $profilePicPath,
                'dob'           => $request->dob,
                'blood_group'   => $request->blood_group,
                'gender'        => $request->gender,
                'created_by'    => $authUser->id,
            ]);

            
            // Save multiple contacts
            if (is_array($request->contacts)) {
                foreach ($request->contacts as $contact) {
                    UserContact::create([
                        'user_id'       => $user->id,
                        'name'          => $contact['name'] ?? null,
                        'factory_name'  => $contact['factory_name'] ?? null,
                        'role'          => $contact['role'] ?? null,
                        'phone'         => $contact['phone'] ?? null,
                        'email'         => $contact['email'] ?? null,
                        'whatsapp'      => $contact['whatsapp'] ?? null,
                        'imo'           => $contact['imo'] ?? null,
                        'facebook'      => $contact['facebook'] ?? null,
                        'linkedin'      => $contact['linkedin'] ?? null,
                        'address'       => $contact['address'] ?? null,
                    ]);
                }
            }

            DB::commit();
            return success_response(null, 'Lead has been created');
        } catch (\Exception $e) {
            DB::rollBack();
            return error_response($e->getMessage(), 500);
        }
    } 

    public function customerToLead($id){
        $user = User::find($id);
        $authUser = Auth::user();
        $customer = Customer::create([
            'user_id'         => $user->id,
            'lead_source_id'  => null,
            'referred_by'     => $authUser->id,
            'created_by'      => $authUser->id,
        ]);

        $followup_category = FollowupCategory::orderBy('serial', 'asc')->first();
            $pipeline = SalesPipeline::create([
                'user_id'               => $user->id,
                'customer_id'           => $customer->id,
                'service_id'            => null,
                'service_details'       => null,
                'qty'                   => null,
                'followup_categorie_id'=> $followup_category->id,
                'assigned_to'           => $authUser->id,
                'type'                  => "lead_data",
            ]);
 
            $leadCategory = FollowupCategory::where('status', 1)->first();
            FollowupLog::create([
                'user_id'               => $user->id,
                'followup_categorie_id' => $leadCategory->id,
                'customer_id'           => $customer->id,
                'pipeline_id'           => $pipeline->id,
                'followup_category_id'  => $followup_category->id,
                'notes'                 => "Created as lead to customer data",
                'created_by'            => $authUser->id,
            ]);

        
    }



  
}
