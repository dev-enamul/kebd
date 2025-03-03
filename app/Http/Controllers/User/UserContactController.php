<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserContact;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserContactController extends Controller
{
    public function contact_data($user_id){
        try{
             $user_contacts = UserContact::where('user_id',$user_id)->get();
             return success_response($user_contacts);
        }catch(Exception $e){
             return error_response($e->getMessage());
        }
     }
 
     public function add_contact(Request $request)
     {
         $request->validate([
             'user_id' => 'required',
             'name' => 'nullable|string|max:255', 
             'role' => 'nullable|string|max:20',
             'phone' => 'nullable|string|max:20',
             'email' => 'nullable|email|max:45',
             'address' => 'nullable|email|max:45', 
             'whatsapp' => 'nullable|string|max:20',
             'imo' => 'nullable|string|max:20',
             'facebook' => 'nullable|string|max:100',
             'linkedin' => 'nullable|string|max:100',
         ]); 
         try {
             UserContact::create([
                'user_id' => $request->user_id,
                'name' => $request->name,
                'role' => $request->role,
                'phone' => $request->phone,
                'email' => $request->email,
                'address' => $request->address,
                'whatsapp' => $request->whatsapp, 
                'imo' => $request->imo,
                'facebook' => $request->facebook,
                'linkedin' => $request->linkedin,  
             ]); 
             return success_response(null, "Contact added successfully"); 
         } catch (Exception $e) { 
             return error_response($e->getMessage(), 500, "An error occurred while adding the contact");
         }
     }
 
     public function update_contact(Request $request, $id)
     { 
         $request->validate([ 
             'name' => 'nullable|string|max:255', 
             'role' => 'nullable|string|max:20',
             'phone' => 'nullable|string|max:20',
             'email' => 'nullable|email|max:45',
             'address' => 'nullable|email|max:45', 
             'whatsapp' => 'nullable|string|max:20',
             'imo' => 'nullable|string|max:20',
             'facebook' => 'nullable|string|max:100',
             'linkedin' => 'nullable|string|max:100',
         ]);   

         try { 
            $userContact = UserContact::find($id);
            if(!$userContact){
                return error_response(null,404,"User not found");
            }    
            $userContact->update([
                'name' => $request->name, 
                'role' => $request->role,
                'phone' => $request->phone,
                'email' => $request->email,
                'address' => $request->address,
                'whatsapp' => $request->whatsapp, 
                'imo' => $request->imo,
                'facebook' => $request->facebook,
                'linkedin' => $request->linkedin,  
            ]);  

            return success_response(null, "Contact updated successfully"); 
         } catch (Exception $e) { 
             return error_response($e->getMessage(), 500, "An error occurred while updating the contact");
         }
     } 
 
     public function show_contact($id)
     { 
         $userContact = UserContact::find($id); 
         if (!$userContact) { 
             return error_response(null, 404, "User contact not found");
         } 
         return success_response($userContact, "Contact retrieved successfully");
     }
}
