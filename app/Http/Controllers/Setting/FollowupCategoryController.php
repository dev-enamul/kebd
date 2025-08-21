<?php

namespace App\Http\Controllers\Setting;

use App\Http\Controllers\Controller;
use App\Models\FollowupCategory;
use App\Models\FollowupLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Exception;

class FollowupCategoryController extends Controller
{
    /**
     * Display a listing of the followup categories
     */
    public function index(Request $request)
    {
        try {
            $employeeId = $request->employee_id ?? null;

            // Single cache key
            $categories = Cache::rememberForever('followup_categories', function () {
                $allCategories = FollowupCategory::select('id','title','slug','status','serial')
                                    ->orderBy('serial','asc')
                                    ->get();

                // Group by status inside cache
                return $allCategories->groupBy('status');
            });

            // Status filter
            $status = $request->status;
            if ($status !== null) {
                $categories = $categories[$status] ?? collect();
            } else {
                // Merge all statuses
                $categories = $categories->flatten();
            }

            // Add followup count
            $datas = $categories->map(function($query) use ($employeeId){
                return [
                    'id'     => $query->id,
                    'title'  => $query->title,
                    'slug'   => $query->slug,
                    'status' => $query->status,
                    'serial' => $query->serial,
                    'value'  => $query->countFollowup($employeeId),
                ];
            });

            return success_response($datas);

        } catch (Exception $e) {
            return error_response($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Store a newly created resource
     */
    public function store(Request $request)
    {
        try {
            $model = new FollowupCategory();
            $model->create([
                'title'  => $request->title,
                'slug'   => getSlug($model, $request->title),
                'status' => $request->status ?? 1,
                'serial' => $request->serial,
            ]);

            // Clear cache
            $this->clearCache();

            return success_response("Followup category created successfully");
        } catch (Exception $e) {
            return error_response($e->getMessage(), 500);
        }
    }

    /**
     * Display the specified resource
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
     * Update the specified resource
     */
    public function update(Request $request, string $id)
    {
        try {
            $category = FollowupCategory::find($id);
            $category->update([
                'title'  => $request->title,
                'slug'   => getSlug($category, $request->title),
                'status' => $request->status ?? 1,
                'serial' => $request->serial,
            ]);

            // Clear cache
            $this->clearCache();

            return success_response('Followup category updated successfully');
        } catch (Exception $e) {
            return error_response($e->getMessage(), 500);
        }
    }

    /**
     * Remove the specified resource
     */
    public function destroy(string $id)
    {
        try {
            $category = FollowupCategory::find($id);
            $followupLog = FollowupLog::where('followup_categorie_id', $id)->count();

            if ($followupLog > 0) {
                return error_response(
                    'This followup category is currently associated with existing followups and cannot be deleted. You may choose to deactivate it instead.',
                    400
                );
            }

            $category->delete();

            // Clear cache
            $this->clearCache();

            return success_response("Followup category deleted successfully");
        } catch (Exception $e) {
            return error_response($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Clear followup_categories cache
     */
    private function clearCache()
    {
        Cache::forget('followup_categories');
    }
}
