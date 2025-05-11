<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class EmployeeStoreResource extends FormRequest
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
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:users,email',  
            'phone' => 'required|string|max:20|unique:users,phone',  
            'designation_id' => 'required|exists:designations,id',   
            'reporting_user_id' => 'nullable|exists:users,id',  
            'dob' => 'nullable|date', 
            'blood_group' => 'nullable|in:A+,A-,B+,B-,AB+,AB-,O+,O-',  
            'gender' => 'nullable|in:male,female,others',  
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',  
            
            // User Contact Details
            'office_phone' => 'nullable|string|max:20',
            'personal_phone' => 'nullable|string|max:20',
            'office_email' => 'nullable|email|max:45',
            'personal_email' => 'nullable|email|max:45',
            'whatsapp' => 'nullable|string|max:20',
            'imo' => 'nullable|string|max:20',
            'facebook' => 'nullable|string|max:100',
            'linkedin' => 'nullable|string|max:100',

            // User Address Details
            'permanent_country' => 'nullable|string|max:255',
            'permanent_division' => 'nullable|string|max:255',
            'permanent_district' => 'nullable|string|max:255',
            'permanent_upazila_or_thana' => 'nullable|string|max:255',
            'permanent_zip_code' => 'nullable|string|max:255',
            'permanent_address' => 'nullable|string|max:500',
            'is_same_present_permanent' => 'nullable|boolean',

            'present_country' => 'nullable|required_if:is_same_present_permanent,false|string|max:255',
            'present_division' => 'nullable|required_if:is_same_present_permanent,false|string|max:255',
            'present_district' => 'nullable|required_if:is_same_present_permanent,false|string|max:255',
            'present_upazila_or_thana' => 'nullable|required_if:is_same_present_permanent,false|string|max:255',
            'present_zip_code' => 'nullable|required_if:is_same_present_permanent,false|string|max:255',
            'present_address' => 'nullable|required_if:is_same_present_permanent,false|string|max:500', 
        ];
    }
}
