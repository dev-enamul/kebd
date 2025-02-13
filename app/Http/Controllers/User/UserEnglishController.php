<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\UserEnglish;
use Illuminate\Http\Request;

class UserEnglishController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'certificate_name' => 'nullable|string',
            'score' => 'nullable|string',
        ]);

        try {
             UserEnglish::create($validated); 
            return success_response(null, 'English created successfully');
        } catch (\Exception $e) {
            return error_response($e->getMessage(), 500, 'Failed to create English');
        }
    }
 
    public function index()
    {
        try {
            $userEnglishes = UserEnglish::all();
            return success_response($userEnglishes);
        } catch (\Exception $e) {
            return error_response($e->getMessage(), 500, 'Failed to fetch User Englishes');
        }
    }  

    public function show($id)
    {
        try {
            $userEnglish = UserEnglish::findOrFail($id);
            return success_response($userEnglish, 'User English fetched successfully');
        } catch (\Exception $e) {
            return error_response($e->getMessage(), 404, 'User English not found');
        }
    }
 
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'certificate_name' => 'nullable|string',
            'score' => 'nullable|string',
        ]);

        try {
            $userEnglish = UserEnglish::findOrFail($id);
            $userEnglish->update($validated);

            return success_response(null, 'English updated successfully');
        } catch (\Exception $e) {
            return error_response($e->getMessage(), 500, 'Failed to update English');
        }
    }
 
    public function destroy($id)
    {
        try {
            $userEnglish = UserEnglish::findOrFail($id);
            $userEnglish->delete();

            return success_response(null, 'User English deleted successfully');
        } catch (\Exception $e) {
            return error_response($e->getMessage(), 500, 'Failed to delete User English');
        }
    }
}
