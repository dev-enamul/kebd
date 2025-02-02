<?php

namespace App\Http\Controllers;

use App\Http\Requests\FileUploadRequest;
use App\Models\UserFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;


class FileControllr extends Controller
{  
    public function store(FileUploadRequest $request){
        if ($request->hasFile('file')) {
            $file = $request->file('file'); 
            $fileName = $request->input('name') . '_' . time() . '.' . $file->getClientOriginalExtension(); 
            $destinationPath = public_path('user_files');  
            $file->move($destinationPath, $fileName); 
            $filePath = 'user_files/' . $fileName;  

            $fileData = UserFile::create([
                'user_id' => $request->user_id,
                'file_name' => $request->input('name'),
                'file_path' => $filePath,
                'file_type' => $file->getClientMimeType(),
                'file_size' => 11,
            ]);
    
            return response()->json(['message' => 'File uploaded successfully', 'file' => $fileData], 201);
        }
    
        return response()->json(['message' => 'No file uploaded.'], 400);
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
