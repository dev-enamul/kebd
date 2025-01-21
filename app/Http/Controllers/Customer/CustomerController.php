<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\User;
use App\Models\UserAddress;
use App\Models\UserContact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
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
            ]); 
 
            UserContact::create([
                'user_id'           => $user->id,
                'name'              => $request->name,
                'relationship_or_role' => "Customer",
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
                    'address'           => $request->present_address,
                    "is_same_present_permanent" => $request->is_same_present_permanent
                ]);
            }
 
            $customer = Customer::create([
                'user_id' => $user->id,
                'customer_id' => Customer::generateNextCustomerId(),
                'lead_source_id' => $request->lead_source_id,
                'ref_id' => $request->ref_id,
                'designation_id'=> $request->designation_id,
                'status' => 1,
            ]); 

            // Create Employee Designation
            DesignationLog::create([
                'user_id' => $user->id,
                'employee_id' => $employee->id,
                'designation_id' => $request->designation_id,
                'start_date' => now() 
            ]); 

            UserReporting::create([
                'user_id' => $user->id, 
                'reporting_user_id' => $request->reporting_user_id,
                'start_date' => now() 
            ]);
 
            $user->save();
            
            DB::commit();  
            return success_response(null,'Employee has been created'); 

        } catch (\Exception $e) { 
            DB::rollBack();  
            return error_response($e->getMessage(), 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
