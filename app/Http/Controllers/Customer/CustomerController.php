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
        try {
            $datas = User::where('user_type', 'customer')
                ->whereHas('customer', function ($q) {
                    $q->where('total_sales', '>', 0);
                })
                ->with('customer')
                ->get()
                ->map(function ($user) {
                    return [
                        'name' => $user->name,
                        'email' => $user->email,
                        'phone' => $user->phone,
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
