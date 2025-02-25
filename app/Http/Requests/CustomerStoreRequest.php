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
            'project_name'      => 'required|string|max:255',
            'client_name'       => 'required|string|max:255',
            'service_id'       => 'required|exists:services,id', 
            'qty'               => 'nullable|integer',  

            'client_office_address'      => 'required|string|max:255',
            'client_office_person'      => 'required|string|max:255',
            'client_office_phone'      => 'required|string|max:255',
            'client_office_email'      => 'required|string|max:255',

            'site_address'      => 'required|string|max:255',
            'site_person'      => 'required|string|max:255',
            'site_phone'      => 'required|string|max:255',
            'site_email'      => 'required|string|max:255', 
        ];
    }
}
