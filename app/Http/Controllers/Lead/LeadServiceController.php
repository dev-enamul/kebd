<?php

namespace App\Http\Controllers\Lead;

use App\Http\Controllers\Controller;
use App\Models\SalesPipeline;
use Illuminate\Http\Request;

class LeadServiceController extends Controller
{
    public function show($id){
        $lead = SalesPipeline::find($id);
        if(!$lead){
            return error_response(null,404,"Lead not found");
        } 

        $datas = [
            'service_id' => @$lead->service_id??"-",
            'service' => @$lead->service->title??"", 
            'qty' => @$lead->qty??"-",
            'service_details' => @$lead->service_details??"-",
        ];
        return success_response($datas);
    }

    public function update(Request $request, $id){ 
        $lead = SalesPipeline::find($id);
        if(!$lead){
            return error_response(null,404,"Invalid Lead Id");
        }  
        $lead->service_id = $request->service_id;
        $lead->qty = $request->qty;
        $lead->service_details = $request->service_details;
        $lead->save();
        return success_response(null,"Service details have been successfully updated.");
    }
}
