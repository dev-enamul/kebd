<?php

namespace App\Http\Controllers\Setting;

use App\Http\Controllers\Controller;
use App\Models\FollowupCategory;
use App\Models\FollowupLog;
use Exception;
use Illuminate\Http\Request;

class FollowupCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $categories = FollowupCategory::where('status', 1)->select('id','title','slug','status','serial')->orderBy('serial','asc')->get();
            return success_response($categories);
        } catch (Exception $e) {
            return error_response($e->getMessage(), $e->getCode());
        }
    }
 
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $model = new FollowupCategory();
            $model->create([
                'title' => $request->title,
                'slug' => getSlug($model, $request->title),
                'status' => $request->status ?? 1,  
                'serial' => $request->serial,  
            ]);
            return success_response("Followup category created successfully");
        } catch (Exception $e) {
            return error_response($e->getMessage(), 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $category = FollowupCategory::find($id);
            return success_response($category);
        } catch (Exception $e) {
            return error_response($e->getMessage(), 500);
        }
    }
 

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $category = FollowupCategory::find($id);
            $category->update([
                'title' => $request->title,
                'slug' => getSlug($category, $request->title),
                'status' => $request->status ?? 1,  
                'serial' => $request->serial, 
            ]);
            return success_response('Followup category updated successfully');
        } catch (Exception $e) {
            return error_response($e->getMessage(), 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $category = FollowupCategory::find($id);
            $followupLog = FollowupLog::where('followup_categorie_id', $id)->count();
            if($followupLog>0){
                return error_response('This followup category is currently associated with existing followups and cannot be deleted. You may choose to deactivate it instead.', 400);
            }
            $category->delete();
            return success_response("Followup category deleted successfully");
        } catch (Exception $e) {
            return error_response($e->getMessage(), $e->getCode());
        }
    }
}
