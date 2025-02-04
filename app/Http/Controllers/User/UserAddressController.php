<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserAddress;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserAddressController extends Controller
{
    public function show($user_id){
        try { 
           
            $address = UserAddress::where('user_id', $user_id)->get();  
            return success_response($address);
        } catch (Exception $e) { 
            return error_response($e->getMessage(), 500);
        }
    }
    

    public function update(Request $request) {
        $request->validate([
            'user_id' => 'required',
            'permanent_country' => 'required|string|max:255',
            'permanent_division' => 'required|string|max:255',
            'permanent_district' => 'required|string|max:255',
            'permanent_upazila_or_thana' => 'required|string|max:255',
            'permanent_zip_code' => 'nullable|string|max:20',
            'permanent_address' => 'nullable|string|max:500',
            'is_same_present_permanent' => 'required|boolean',
            'present_country' => 'required_if:is_same_present_permanent,false|string|max:255',
            'present_division' => 'required_if:is_same_present_permanent,false|string|max:255',
            'present_district' => 'required_if:is_same_present_permanent,false|string|max:255',
            'present_upazila_or_thana' => 'required_if:is_same_present_permanent,false|string|max:255',
            'present_zip_code' => 'nullable|string|max:20',
            'present_address' => 'nullable|string|max:500',
        ]);
    
        try{ 
           $ex_permanent =  UserAddress::where('user_id', $request->user_id)->where('address_type','permanent')->first();
           if($ex_permanent){
            $ex_permanent->update([  
                'country' => $request->permanent_country,
                'division' => $request->permanent_division,
                'district' => $request->permanent_district,
                'upazila_or_thana' => $request->permanent_upazila_or_thana,
                'zip_code' => $request->permanent_zip_code,
                'address' => $request->permanent_address, 
                'is_same_present_permanent' => $request->is_same_present_permanent,
            ]);
           }else{
                UserAddress::create([
                'user_id' => $request->user_id,
                'address_type' => 'permanent',
                'country' => $request->permanent_country,
                'division' => $request->permanent_division,
                'district' => $request->permanent_district,
                'upazila_or_thana' => $request->permanent_upazila_or_thana,
                'zip_code' => $request->permanent_zip_code,
                'address' => $request->permanent_address, 
                'is_same_present_permanent' => $request->is_same_present_permanent, 
            ]);
           }
           
        
            if (!$request->is_same_present_permanent) {
                $ex_present =  UserAddress::where('user_id', $request->user_id)->where('address_type','present')->first();
                if($ex_present){
                    $ex_present->update([  
                        'country' => $request->present_country,
                        'division' => $request->present_division,
                        'district' => $request->present_district,
                        'upazila_or_thana' => $request->present_upazila_or_thana,
                        'zip_code' => $request->present_zip_code,
                        'address' => $request->present_address, 
                        'is_same_present_permanent' => $request->is_same_present_permanent, 
                    ]);
                }else{
                    UserAddress::create([
                        'user_id' => $request->user_id,
                        'address_type' => 'present',
                        'country' => $request->present_country,
                        'division' => $request->present_division,
                        'district' => $request->present_district,
                        'upazila_or_thana' => $request->present_upazila_or_thana,
                        'zip_code' => $request->present_zip_code,
                        'address' => $request->present_address, 
                        'is_same_present_permanent' => $request->is_same_present_permanent, 
                    ]);
                }
                
            } 
            return success_response(null,"User address updated successfully");
        }catch(Exception $e){
            return error_response($e->getMessage(),500);
        }
    }
}
