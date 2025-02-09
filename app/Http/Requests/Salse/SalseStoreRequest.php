<?php

namespace App\Http\Requests\Salse;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class SalseStoreRequest extends FormRequest
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
            'user_id' => 'required|exists:users,id', 
            'lead_id' => 'nullable|exists:sales_pipelines,id',
            'price' => 'required|numeric',
            'payment_schedule_amount' => 'required|numeric',
            'payment_schedule' => 'nullable|array',
            'payment_schedule.*.date' => 'required|date',
            'payment_schedule.*.amount' => 'required|numeric',
        ];
    }
}
