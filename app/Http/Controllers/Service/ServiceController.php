<?php

namespace App\Http\Controllers\Service;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Exception;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $services = Service::where('status',1)->select('name','slug','description','regular_price','sell_price')->get();
        return success_response($services);
    }
  
    public function store(Request $request)
    {
        try{
            $service = new Service();
            $service->create([
                "title" => $request->title,
                "slug" => getSlug($service, $request->title),
                "description" => $request->description,
                "regular_price" => $request->regular_price,
                "sell_price" => $request->sell_price,
            ]);
             return success_response('Service created successfully');
        }catch(Exception $e){
            return error_response($e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $service = Service::find($id);
        if(!$service){
            return error_response('Service not found');
        }
        return success_response($service);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try{
            $service = Service::find($id);
            if(!$service){
                return error_response('Service not found');
            }
            $service->update([
                "title" => $request->title,
                "slug" => getSlug($service, $request->title),
                "description" => $request->description,
                "regular_price" => $request->regular_price,
                "sell_price" => $request->sell_price,
            ]);
            return success_response('Service updated successfully');
        }catch(Exception $e){
            return error_response($e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try{
            $service = Service::find($id);
            if(!$service){
                return error_response('Service not found');
            }
            $service->delete();
            return success_response('Service deleted successfully');
        }catch(Exception $e){
            return error_response($e->getMessage());
        }
    }
}
