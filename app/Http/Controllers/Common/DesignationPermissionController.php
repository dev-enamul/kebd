<?php

namespace App\Http\Controllers\Common;

use App\Http\Controllers\Controller;
use App\Models\Designation;
use App\Models\DesignationPermission;
use App\Models\Permission;
use Exception;
use Illuminate\Http\Request;

class DesignationPermissionController extends Controller
{
    public function show($designation_id){ 
        $designation = Designation::find($designation_id); 
        $designation_permission = $designation->permissions; 
        $all_permission = Permission::get(); 
        $permission = []; 
        foreach ($all_permission as $perm) {
            $permission[] = [
                'id' => $perm->id,
                'name' => $perm->name,
                'is_checked' => $designation_permission->contains(function($designationPerm) use ($perm) {
                    return $designationPerm->pivot->permission_id == $perm->id;
                })
            ];
        }
    
        return $permission; 
    }  

    public function update(Request $request, $designation_id) {
        try { 
            DesignationPermission::where('designation_id', $designation_id)->delete(); 
            $permissions = $request->permissions; 
            if (isset($permissions) && count($permissions) > 0) { 
                foreach ($permissions as $permission) {
                    DesignationPermission::create([
                        'designation_id' => $designation_id,
                        'permission_id'  => $permission,
                    ]);
                }
            } 
            return success_response(null, "Designation updated");
        } catch (Exception $e) { 
            return error_response($e->getMessage());
        }
    }
    
}
