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
            'project_name'  => 'required|string|max:255',
            'client_name'   => 'required|string|max:255',
            'service_id'    => 'nullable|exists:services,id',
            'qty'           => 'nullable|integer',
            'lead_source_id'=> 'nullable|exists:lead_sources,id',
            'referred_by'   => 'nullable|string|max:255',
            'dob'           => 'nullable|date',
            'blood_group'   => 'nullable|string|max:10',
            'gender'        => 'nullable|in:male,female,other',
            'notes'         => 'nullable|string',
    
            'contacts'                  => 'required|array|min:1',
            'contacts.*.name'           => 'required|string|max:255',
            'contacts.*.factory_name'   => 'nullable|string|max:255',
            'contacts.*.role'           => 'nullable|string|max:255',
            'contacts.*.phone'          => 'required|string|max:255',
            'contacts.*.email'          => 'nullable|email|max:255',
            'contacts.*.whatsapp'       => 'nullable|string|max:255',
            'contacts.*.imo'            => 'nullable|string|max:255',
            'contacts.*.facebook'       => 'nullable|string|max:255',
            'contacts.*.linkedin'       => 'nullable|string|max:255',
            'contacts.*.address'        => 'nullable|string|max:255',
        ];
    }
}
