<?php

namespace App\Http\Controllers\Auth;

use App\Helpers\LoginService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\Company;
use App\Models\Employee;
use App\Models\EmployeeDesignation;
use App\Models\User;
use App\Models\UserAddress;
use App\Models\UserContact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(LoginRequest $request)
    {     
        
        $request->authenticate(); 
        $user = Auth::user();  
        return LoginService::createResponse($user);
    }  

    public function updatePassword(Request $request)
    { 
        $request->validate([
            'id'    => ['required'], 
            'current_password' => ['required'],
            'new_password' => ['required', 'min:8', 'confirmed'],
        ]);  
        $user = User::find($request->id);
        if (!Hash::check($request->current_password, $user->password)) {
            return error_response(null,422,'Current password is incorrect'); 
        } 
        $user->password = Hash::make($request->new_password);
        $user->save(); 
        return success_response(null,"Password updated successfully"); 
    } 

    public function resetPassword($id)
    {
        try {
            $user = User::find($id);
            if (!$user) {
                return error_response(null, 404, 'User not found');
            } 
            $user->password = Hash::make('123456');
            $user->save();   
            return success_response(null, "Password reset successfully. Default password: 123456");
        } catch (\Exception $e) {
            return error_response(null, 500, $e->getMessage());
        }
    }
}
