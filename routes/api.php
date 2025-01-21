<?php

use App\Http\Controllers\Admin\Employee\DesignationController;
use App\Http\Controllers\AdminEmployee\EmployeeController;
use App\Http\Controllers\Auth\AuthController; 
use App\Http\Controllers\Common\CountryApiController; 
use App\Http\Controllers\Common\DistrictApiController;
use App\Http\Controllers\Common\DivisionApiController;
use App\Http\Controllers\Common\RoleApiController;
use App\Http\Controllers\Common\UnionApiController;
use App\Http\Controllers\Common\UpazilaApiController;
use App\Http\Controllers\Customer\CustomerController;
use App\Http\Controllers\Followup\FollowupCategoryController;
use App\Http\Controllers\Service\ServiceController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/
Route::post('login', [AuthController::class, 'login'])->name('login');  
Route::get('roles',RoleApiController::class);

// Location 
Route::get('countries',CountryApiController::class);
Route::get('divisions',DivisionApiController::class);
Route::get('districts',DistrictApiController::class);
Route::get('upazilas',UpazilaApiController::class);
Route::get('unions',UnionApiController::class);  
 
Route::middleware(['auth:sanctum'])->group(function () {
    Route::resource('designation', DesignationController::class);
    Route::resource('followup-category', FollowupCategoryController::class);
    Route::resource('employee', EmployeeController::class);
    Route::resource('service', ServiceController::class);
    Route::resource('customer', CustomerController::class);
});


 
