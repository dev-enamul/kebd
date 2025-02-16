<?php

namespace App\Http\Controllers\Bank;

use App\Http\Controllers\Controller;
use App\Models\Bank;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BankController extends Controller
{
    public function index()
    {
        $banks = Bank::all();
        return success_response($banks);
    }
 
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'            => 'required|string|max:255',
            'account_number'  => 'required|string|unique:banks,account_number|max:50',
            'balance'         => 'nullable|numeric|min:0',
            'branch'          => 'nullable|string|max:255',
            'account_holder'  => 'nullable|string|max:255',
            'swift_code'      => 'nullable|string|max:50',
            'iban'            => 'nullable|string|max:50',
            'currency'        => 'nullable|string|max:10',
            'contact_number'  => 'nullable|string|max:20',
            'email'           => 'nullable|email|max:255',
            'address'         => 'nullable|string',
            'status'          => 'boolean',
        ]); 
        if ($validator->fails()) {
            return error_response($validator->errors(), 422, 'Validation Error');
        } 
        Bank::create($request->all()); 
        return success_response(null,'Bank created successfully', 201);
    }  

    public function show($id)
    {
        $bank = Bank::find($id);
        if (!$bank) {
            return error_response(null, 404, 'Bank not found');
        } 
        return success_response($bank);
    }

    
    public function update(Request $request, $id)
    {
        $bank = Bank::find($id);
        if (!$bank) {
            return error_response(null, 404, 'Bank not found');
        }

        $validator = Validator::make($request->all(), [
            'name'            => 'sometimes|string|max:255',
            'account_number'  => 'sometimes|string|unique:banks,account_number,' . $id . '|max:50',
            'balance'         => 'nullable|numeric|min:0',
            'branch'          => 'nullable|string|max:255',
            'account_holder'  => 'nullable|string|max:255',
            'swift_code'      => 'nullable|string|max:50',
            'iban'            => 'nullable|string|max:50',
            'currency'        => 'nullable|string|max:10',
            'contact_number'  => 'nullable|string|max:20',
            'email'           => 'nullable|email|max:255',
            'address'         => 'nullable|string',
            'status'          => 'boolean',
        ]);

        if ($validator->fails()) {
            return error_response($validator->errors(), 422, 'Validation Error');
        }

        $bank->update($request->all());
        return success_response($bank, 'Bank updated successfully');
    }  

    public function destroy($id)
    {
        $bank = Bank::find($id);
        if (!$bank) {
            return error_response(null, 404, 'Bank not found');
        }

        $bank->delete();
        return success_response(null, 'Bank deleted successfully');
    }
}
