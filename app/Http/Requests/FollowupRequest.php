<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class FollowupRequest extends FormRequest
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
            'lead_id' => 'required|exists:sales_pipelines,id', 
            'followup_categorie_id' => 'required|exists:followup_categories,id',  
            'purchase_probability' => 'nullable|integer|between:0,100', 
            'price' => 'nullable|numeric|min:0', 
            'next_followup_date' => 'nullable|date|after_or_equal:today',  
            'service_ids' => 'nullable|array',  
            'service_ids.*' => 'integer|exists:services,id', 
            'notes' => 'nullable|string|max:1000',
        ];
    }
}
