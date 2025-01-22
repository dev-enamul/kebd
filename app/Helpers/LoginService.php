<?php
namespace App\Helpers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\AffiliatePayoutInfo;
use App\Models\Permission;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\URL;
use Illuminate\Validation\ValidationException;

class LoginService {

    public static function createResponse($user)
    {
        $token = $user->createToken('authToken')->plainTextToken;
 
        $permissions = $user->employee->designation->slug == 'admin'
            ? Permission::pluck('slug')  
            : $user->employee->designation->permission->pluck('slug'); 
     
        $data = [
            'token' => $token,
            'user' => [
                'name' => $user->name,
                'email' => $user->email, 
                'designation' => $user->employee->designation,
            ],
            'permissions' => $permissions,
        ];   
        return success_response($data, 'User authenticated successfully.'); 
    }

}