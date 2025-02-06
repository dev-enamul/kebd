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
                'file_type' => $file->getClientMimeType(),
                'file_size' => $file->getSize(),  
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
 
        $file = UserFile::findOrFail($id);
 
        if ($request->hasFile('file')) { 
            if (File::exists(public_path($file->file_path))) {
                File::delete(public_path($file->file_path));
            } 
            $uploadedFile = $request->file('file'); 
            $fileName = $request->input('name', $file->file_name) . '_' . time() . '.' . $uploadedFile->getClientOriginalExtension();
 
            $destinationPath = public_path('user_files'); 
            $uploadedFile->move($destinationPath, $fileName); 
            $file->file_name = $fileName;
            $file->file_path = 'user_files/' . $fileName;
            $file->file_type = $uploadedFile->getClientMimeType();
            $file->file_size = $uploadedFile->getSize();
        }
 
        if ($request->has('name')) {
            $file->file_name = $request->input('name') . '_' . time() . '.' . $file->getClientOriginalExtension();
        }

        // Save the updated file information
        $file->save();

        return response()->json(['message' => 'File updated successfully', 'file' => $file], 200);
    }

    
}
