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
             $user_contacts = UserContact::where('user_id',$user_id)->first();
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
             'relationship_or_role' => 'nullable|string|max:255',
             'office_phone' => 'nullable|string|max:20',
             'personal_phone' => 'nullable|string|max:20',
             'office_email' => 'nullable|email|max:45',
             'personal_email' => 'nullable|email|max:45',
             'website' => 'nullable|string',
             'whatsapp' => 'nullable|string|max:20',
             'imo' => 'nullable|string|max:20',
             'facebook' => 'nullable|string|max:100',
             'linkedin' => 'nullable|string|max:100',
         ]); 
         try {
             UserContact::create([
                 'user_id' => $request->user_id,
                 'name' => $request->name,
                 'relationship_or_role' => $request->relationship_or_role,
                 'office_phone' => $request->office_phone,
                 'personal_phone' => $request->personal_phone,
                 'office_email' => $request->office_email,
                 'personal_email' => $request->personal_email,
                 'website' => $request->website,
                 'whatsapp' => $request->whatsapp,
                 'imo' => $request->imo,
                 'facebook' => $request->facebook,
                 'linkedin' => $request->linkedin
             ]); 
             return success_response(null, "Contact added successfully"); 
         } catch (Exception $e) { 
             return error_response($e->getMessage(), 500, "An error occurred while adding the contact");
         }
     }
 
     public function update_contact(Request $request)
     { 
         $request->validate([
             'user_id'   => 'required',
             'name' => 'nullable|string|max:255', 
             'office_phone' => 'nullable|string|max:20',
             'personal_phone' => 'nullable|string|max:20',
             'office_email' => 'nullable|email|max:45',
             'personal_email' => 'nullable|email|max:45',
             'website' => 'nullable|string',
             'whatsapp' => 'nullable|string|max:20',
             'imo' => 'nullable|string|max:20',
             'facebook' => 'nullable|string|max:100',
             'linkedin' => 'nullable|string|max:100',
         ]);  
         try { 
            $userContact = UserContact::where('user_id',$request->user_id)->first(); 
            if (!$userContact) {
                UserContact::create([
                    'user_id' => $request->user_id,
                    'relationship_or_role' => "Ownself",
                    'name' => $request->name, 
                    'office_phone' => $request->office_phone,
                    'personal_phone' => $request->personal_phone,
                    'office_email' => $request->office_email,
                    'personal_email' => $request->personal_email,
                    'website' => $request->website,
                    'whatsapp' => $request->whatsapp,
                    'imo' => $request->imo,
                    'facebook' => $request->facebook,
                    'linkedin' => $request->linkedin,  
                ]);
            }else{
                $userContact->update([
                    'name' => $request->name, 
                    'office_phone' => $request->office_phone,
                    'personal_phone' => $request->personal_phone,
                    'office_email' => $request->office_email,
                    'personal_email' => $request->personal_email,
                    'website' => $request->website,
                    'whatsapp' => $request->whatsapp,
                    'imo' => $request->imo,
                    'facebook' => $request->facebook,
                    'linkedin' => $request->linkedin,  
                ]); 
            } 
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
