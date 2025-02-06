<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;

class UserUpdateController extends Controller
{
    public function update_profile_picture(Request $request){
        try{
            $request->validate([
                'user_id' => 'required|exists:users,id',
                'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            $profilePicPath = null;
            $user_id = $request->user_id;
    
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $profilePicPath = $image->store('profile_images', 'public');  
                $profilePicUrl = asset('storage/' . $profilePicPath); 
    
            } 
            $user = User::find($user_id);  
            if (!$user) {
                return error_response(null,404,"User not found");
            } 
            $user->update([
                'profile_image' => $profilePicUrl, 
            ]); 
            return success_response([
                "profile_picture" => $profilePicUrl,
            ],"Profile picture updated"); 
    
        }catch(Exception $e){
            return error_response($e->getMessage(), 500);
        }
    }

    public function bio_update(Request $request){
        try{
            $request->validate([
                'user_id' => 'required|exists:users,id',
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email,' . $request->user_id, 
                'dob' => 'nullable|date'
            ]);
            
            
            $user_id = $request->user_id;
            $user = User::find($user_id);
            if (!$user) {
                return error_response(null,404,"User not found");
            }  
            
            $user->update([
                "name" => $request->name,
                'email' => $request->email,
                'phone' => $request->phone, 
                "dob" => $request->dob, 
                'blood_group' => $request->blood_group,
                'gender' => $request->gender 
            ]);
            return success_response(null,"Personal information updated");
        }catch(Exception $e){
            return error_response($e->getMessage(),500);
        } 
    }
}
