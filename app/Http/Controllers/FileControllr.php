<?php

namespace App\Http\Controllers;

use App\Http\Requests\FileUploadRequest;
use App\Models\UserEducation;
use App\Models\UserFile;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;


class FileControllr extends Controller
{  
    public function index(Request $request){
        try{
            $user_id = $request->user_id;
            $education = UserFile::where('user_id',$user_id)->get();
            return success_response($education);
        }catch(Exception $e){
            return error_response($e->getMessage());
        } 
    }
    
    public function store(FileUploadRequest $request){
        if ($request->hasFile('file')) {
            $file = $request->file('file'); 
            $fileName = $request->input('name') . '_' . time() . '.' . $file->getClientOriginalExtension(); 
            $destinationPath = public_path('user_files');  
            $file->move($destinationPath, $fileName);  
            $filePath = url('user_files/' . $fileName);
    
            UserFile::create([
                'user_id' => $request->user_id,
                'file_name' => $request->input('name'),
                'file_path' => $filePath,
                'file_type' => $file->getClientMimeType() 
            ]);
    
            return success_response(null, "File uploaded successfully");
        } 
    
        return error_response("No file uploaded.");
    }

    

    public function update(Request $request, $id)
    {
        
        $request->validate([
            'name' => 'nullable|string',
            'file' => 'nullable|file|mimes:jpg,jpeg,png,pdf,docx,txt|max:2048', 
        ]);

        try{
            $file = UserFile::findOrFail($id); 
            if ($request->hasFile('file')) {
                $file = $request->file('file'); 
                $fileName = $request->input('name') . '_' . time() . '.' . $file->getClientOriginalExtension(); 
                $destinationPath = public_path('user_files');  
                $file->move($destinationPath, $fileName);  
                $filePath = url('user_files/' . $fileName);
        
                $file->update([ 
                    'file_name' => $request->input('name'),
                    'file_path' => $filePath,
                    'file_type' => $file->getClientMimeType() 
                ]);  
            }   
            return success_response(null, "File updated successfully");
        }catch(Exception $e){
            return error_response($e->getMessage());
        }
    } 

    public function destroy($id){
        try{ 
            $file = UserFile::find($id);
             
            if(!$file){
                return error_response(null, 404,"File information not found");
            }
            $file->delete();
            return success_response("File information deleted");
        }catch(Exception $e){
            return error_response($e->getMessage());
        }
    }

    
}
