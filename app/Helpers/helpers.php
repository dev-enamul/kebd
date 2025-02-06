<?php

use App\Models\Permission;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
if (!function_exists('success_response')) {
    function success_response($data = null, $message = 'Success', $statusCode = 200)
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data, 
            'timestamp' => now()->toIso8601String(),
            'request_id' => request()->header('X-Request-ID') ?: uniqid(),
        ], $statusCode);
    }
}

if (!function_exists('error_response')) {
    function error_response( $errors = null, $statusCode = 400, $message = 'An error occurred')
    {
        if ($statusCode == 0) {
            $statusCode = 400;  
        }
        
        return response()->json([
            'success' => false,
            'message' => $message,
            'data' => null,
            'errors' => $errors,
            'timestamp' => now()->toIso8601String(),
            'request_id' => request()->header('X-Request-ID') ?: uniqid(),
        ], $statusCode);
    }
}



if (!function_exists('getSlug')) {
    function getSlug($model, $title, $column = 'slug', $separator = '-') {
        $slug         = Str::slug($title);
        $originalSlug = $slug;
        $count        = 1;

        while ($model::where($column, $slug)->exists()) {
            $slug = $originalSlug . $separator . $count;
            $count++;
        }

        return $slug;
    }
}

if (!function_exists('can')) {
    function can($permission) {
        $user = Auth::user(); 
        $permissions = ($user->employee && $user->employee->designation)
            ? ($user->employee->designation->slug == 'admin'
                ? Permission::pluck('slug')->toArray()
                : $user->employee->designation->permissions->pluck('slug')->toArray()
            )
            : []; 
        return in_array($permission, $permissions);
    }
}


