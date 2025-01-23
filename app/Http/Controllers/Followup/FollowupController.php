<?php

namespace App\Http\Controllers\Followup;

use App\Http\Controllers\Controller;
use App\Models\SalesPipeline;
use Illuminate\Http\Request;

class FollowupController extends Controller
{
    public function index(Request $request){  
    } 

    public function store(Request $request){
        $pipeline = SalesPipeline::find($request->lead_id);
        if(!$pipeline){
            return error_response("Invalid Lead ID", 404);
        }
        $pipeline->update([
            'service_ids'           => $request->service_ids,
            'followup_categorie_id' => $request->followup_categorie_id,
            'followup_status'       => $request->followup_status,
            'last_updated_by'       => auth()->user()->id,
        ]);  
    }
}
