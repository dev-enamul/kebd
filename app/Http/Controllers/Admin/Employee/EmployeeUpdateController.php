<?php

namespace App\Http\Controllers\Admin\Employee;

use App\Helpers\ReportingService;
use App\Http\Controllers\Controller;
use App\Jobs\UpdateReportingJob;
use App\Models\DesignationLog;
use App\Models\SalesPipeline;
use App\Models\User;
use App\Models\UserReporting;
use Carbon\Carbon;
use Exception; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class EmployeeUpdateController extends Controller
{
    public function updateDesignation(Request $request){
        if (!can("manage-employee")) {
            return permission_error_response();
        } 

        DB::beginTransaction(); 
        try {   
            $id = $request->id;
            $designation_id = $request->designation_id; 
            $auth_user = Auth::user(); 
            $user = User::find($id);

            if (!$user) {
                return error_response('User not found.', 404); 
            }
            $employee = $user->employee;
            $employee->designation_id = $designation_id;
            $employee->save();
 
            $employee->designationLog()->update([
                "end_date" => now(),
            ]); 
 
            DesignationLog::create([
                'user_id' => $user->id,
                'employee_id' => $employee->id,
                'designation_id' => $designation_id,
                'created_by' => $auth_user->id,
            ]);
 
            DB::commit(); 
            return success_response('Designation updated successfully.');  
        } catch (Exception $e) {
            DB::rollBack(); 
            return error_response($e->getMessage(),500); 
        }
    } 

    public function updateReporting(Request $request)
    {
        if (!can("manage-employee")) {
            return permission_error_response();
        } 

        try {
            $reporting_user_id = $request->reporting_user_id; 
            $user = User::find($request->id);
            if(!$user){
                return error_response(null,404,"User not found");
            }
            $junior_users = json_decode($user->junior_user??"[]"); 
        
            if ($junior_users && in_array($reporting_user_id, $junior_users)) {
                return error_response("You cannot make this employee your senior because they are already your junior.");
            }
     
            UserReporting::where('user_id', $user->id)
                ->update([
                    'end_date' => now()->subDay() 
                ]);  

            UserReporting::create([
                'user_id' => $user->id,
                'reporting_user_id' => $reporting_user_id,
                'start_date' => now()
            ]);

            UpdateReportingJob::dispatch($reporting_user_id, $user->id);
             
    
            return success_response("Reporting relationship updated successfully.");
        } catch (Exception $e) { 
            return error_response($e->getMessage(), 500);
        }
    }
    

}
