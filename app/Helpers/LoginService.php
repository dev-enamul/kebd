<?php
namespace App\Helpers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\AffiliatePayoutInfo;
use App\Models\Designation;
use App\Models\DesignationPermission;
use App\Models\Permission;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\URL;
use Illuminate\Validation\ValidationException;

class LoginService {

    public static function createResponse($user)
    {
        try { 
            $token = $user->createToken('authToken')->plainTextToken; 
            $permissions = [];
    
            if ($user->employee && $user->employee->designation) {
                $designation = $user->employee->designation;
    
                if ($designation->slug == 'admin') { 
                    $permissions = Permission::pluck('slug')->toArray();
                } else { 
                    if ($designation->permissions) {
                        $permissions = $designation->permissions->pluck('slug')->toArray();
                    }
                }
            } 
            if (empty($permissions)) {
                $permissions = [];
            } 
            $data = [
                'token' => $token,
                'user' => [
                    'name' => $user->name,
                    'email' => $user->email,
                    'designation' => $user->employee?->designation ?? "", 
                ],
                'permissions' => $permissions,
            ];
    
            return success_response($data, 'User authenticated successfully.');
    
        } catch (\Exception $e) { 
            return error_response([], 'An error occurred: ' . $e->getMessage());
        }
    }
    

}