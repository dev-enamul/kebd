<?php

namespace App\Http\Controllers\Notification;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index(Request $request){
        $status = $request->status;
        $limit = $request->limit ?? 20;  
    
        $notifications = Notification::where('user_id', Auth::id())->latest()->select('title','data','link','type','is_read');
    
        if ($status == "all") {
            $datas = $notifications->paginate($limit);
        } else {
            $datas = $notifications->limit(10)->get();  
        } 
        return success_response($datas);
    } 

    public function read($id){
        $notification = Notification::find($id);
        if(!$notification){
            return error_response(null,404,"Notification not found");
        }
        $notification->is_read = true;
        $notification->read_at = now();
        return success_response(null, "Updated read status");
    }
    
}
