<?php

namespace App\Http\Controllers\Admin\Employee;

use App\Http\Controllers\Controller;
use App\Models\Designation;
use App\Models\Employee;
use Exception;
use Illuminate\Http\Request;

class DesignationController extends Controller
{
    
    public function index()
    {
        $designatinos  = Designation::where('status',1)->get();
        return success_response($designatinos);
    }
 
 
    public function store(Request $request)
    {
        try{
            $model = new Designation();
            $model->create([
                'title'=> $request->title,
                'slug' => getSlug($model,$request->title),
            ]);
            return success_response("Designation created successfully");
        }catch(Exception $e){
            return error_response($e->getMessage(),500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try{
            $designation  = Designation::find($id);
            return success_response($designation);
        }catch(Exception $e){
            return error_response($e->getMessage());
        }
    }
 

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try{
            $model = new Designation();
            $designation = $model->find($id);
            $designation->title =  $request->title;
            $designation->slug = getSlug($model,$designation->title);
            $designation->save(); 
            return success_response('Designatin Updates');
        }catch(Exception $e){
            return error_response($e->getMessage(),500);
        }
    }
 
    public function destroy(string $id)
    {
        try{
            $designation  = Designation::find($id);
            $employee_count = Employee::where('designation_id',$id)->count();
            if($employee_count>0){
                return error_response("This Designation has ".$employee_count." employees. You can't delete this desination");
            }
            $designation->delete();
            return success_response("Designation deleted successfully "); 
        }catch(Exception $e){
            return error_response($e->getMessage(), $e->getCode());
        }
        
    }
 
}
