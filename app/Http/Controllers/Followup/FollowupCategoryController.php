<?php

namespace App\Http\Controllers\Followup;

use App\Http\Controllers\Controller;
use App\Models\FollowupCategory;
use Exception;
use Illuminate\Http\Request;

class FollowupCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $designatinos  = FollowupCategory::where('status',1)->get();
        return success_response($designatinos);
    }
 
 
    public function store(Request $request)
    {
        try{
            $model = new FollowupCategory();
            $model->create([
                'title'=> $request->title,
                'slug' => getSlug($model,$request->title),
            ]);
            return success_response("Followup category created successfully");
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
            $designation  = FollowupCategory::find($id);
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
            $model = new FollowupCategory();
            $designation = $model->find($id);
            $designation->title =  $request->title;
            $designation->slug = getSlug($model,$designation->title);
            $designation->save(); 
            return success_response('Folowup category updated successfully');
        }catch(Exception $e){
            return error_response($e->getMessage(),500);
        }
    }
 
    public function destroy(string $id)
    {
        try{
            $designation  = FollowupCategory::find($id);
            // $employee_count = Employee::where('designation_id',$id)->count();
            // if($employee_count>0){
            //     return error_response("This Designation has ".$employee_count." employees. You can't delete this desination");
            // }
            $designation->delete();
            return success_response("Followup category deleted successfully "); 
        }catch(Exception $e){
            return error_response($e->getMessage(), $e->getCode());
        }
        
    }
}
