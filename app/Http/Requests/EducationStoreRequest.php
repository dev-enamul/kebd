<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class EducationStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422)
        );
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
            'institution_name' => 'required|string|max:255',
            'degree' => 'nullable|string|max:255', 
            'field_of_study' => 'nullable|string|max:255', 
            'start_year' => 'nullable|integer|digits:4|before_or_equal:end_year',
            'end_year' => 'nullable|integer|digits:4|after_or_equal:start_year',
            'certificate_path' => 'nullable|string|max:255',
        ];
    }
}
