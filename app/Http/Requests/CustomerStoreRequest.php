<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class CustomerStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */ 

     protected function failedValidation(Validator $validator)
     {
         throw new HttpResponseException(
             response()->json([
                 'message' => 'Validation failed',
                 'errors' => $validator->errors()
             ], 422)
         );
     }

    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name'              => 'required|string|max:255',
            'email'             => 'nullable|email|unique:users,email',
            'phone'             => 'required|string|max:20|unique:users,phone', 
            'service_ids'       => 'nullable|array',
            'service_ids.*'     => 'exists:services,id',   
            'assigned_to'       => 'nullable|exists:users,id',
            'referred_by'       => 'nullable|exists:users,id',   
            'profile_image'     => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
            'dob'               => 'nullable|date',
            'blood_group'       => 'nullable|in:A+,A-,B+,B-,AB+,AB-,O+,O-',
            'gender'            => 'nullable|in:male,female,others', 
            'lead_source_id'    => 'nullable|exists:lead_sources,id', 
            'notes'             => 'nullable|string|max:500', 
            

            // User Contact-related fields
            'office_phone'      => 'nullable|string|max:20',
            'personal_phone'    => 'nullable|string|max:20',
            'office_email'      => 'nullable|email|max:45',
            'personal_email'    => 'nullable|email|max:45',
            'whatsapp'          => 'nullable|string|max:20',
            'imo'               => 'nullable|string|max:20',
            'facebook'          => 'nullable|string|max:100',
            'linkedin'          => 'nullable|string|max:100',

            // User Address-related fields
            'permanent_country' => 'nullable|string|max:255',
            'permanent_division'=> 'nullable|string|max:255',
            'permanent_district'=> 'nullable|string|max:255',
            'permanent_upazila_or_thana' => 'nullable|string|max:255',
            'permanent_zip_code'=> 'nullable|string|max:10',
            'permanent_address' => 'nullable|string|max:500',
            'is_same_present_permanent' => 'nullable|boolean',
            
            // Conditional address for present address
            'present_country'   => 'nullable|string|max:255',
            'present_division'  => 'nullable|string|max:255',
            'present_district'  => 'nullable|string|max:255',
            'present_upazila_or_thana' => 'nullable|string|max:255',
            'present_zip_code'  => 'nullable|string|max:10',
            'present_address'   => 'nullable|string|max:500',
        ];
    }
}
