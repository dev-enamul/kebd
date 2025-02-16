<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Http\Requests\CustomerStoreRequest;
use App\Models\Customer;
use App\Models\FollowupCategory;
use App\Models\FollowupLog;
use App\Models\SalesPipeline;
use App\Models\User;
use App\Models\UserAddress;
use App\Models\UserContact;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (!can("client")) {
            return permission_error_response();
        } 
        try {
            $authUser = User::find(Auth::user()->id); 
            $query = User::where('user_type', 'customer')
                ->whereHas('customer', function ($q) {
                    $q->where('total_sales', '>', 0);
                })
                ->with('customer');

                if(can('all-client')){
                    $datas = $query->get(); 
                }elseif(can('own-team-client')){
                    $juniorUserIds = json_decode($authUser->junior_user ?? "[]");
                    $datas = $query->whereHas('salesPipelines',function($q) use ($juniorUserIds){
                        $q->whereIn('sales_by_user_id', $juniorUserIds);
                    })->get(); 
                }elseif(can('own-client')){
                    $directJuniors = $authUser->directJuniors->pluck('user_id')->toArray(); 
                    $datas = $query->whereHas('salesPipelines',function($q) use ($directJuniors){
                        $q->whereIn('sales_by_user_id', $directJuniors);
                    })->get();  
                }else {
                    $datas = collect();
                }  
                 
                $datas->map(function ($user) {
                    return [ 
                        'name' => $user->name,
                        'email' => $user->email,
                        'phone' => $user->phone,
                        'profile_image' => $user->profile_image,
                        'total_sales' => @$user->customer->total_sales ?? 0,
                        'total_sales_amount' => @$user->customer->total_sales_amount ?? 0,
                    ];
                });
        
            return success_response($datas);
        } catch (Exception $e) {
            return error_response($e->getMessage(), 500);
        }
        
        
    }
  
}
