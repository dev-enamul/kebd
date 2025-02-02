<?php

use App\Http\Controllers\Admin\Employee\DesignationController;
use App\Http\Controllers\Admin\Employee\EmployeeController;
use App\Http\Controllers\Admin\Employee\EmployeeUpdateController;
use App\Http\Controllers\Auth\AuthController; 
use App\Http\Controllers\Common\CountryApiController; 
use App\Http\Controllers\Common\DistrictApiController;
use App\Http\Controllers\Common\DivisionApiController;
use App\Http\Controllers\Common\RoleApiController;
use App\Http\Controllers\Common\UnionApiController;
use App\Http\Controllers\Common\UpazilaApiController;
use App\Http\Controllers\Customer\CustomerController;
use App\Http\Controllers\FileControllr;
use App\Http\Controllers\Followup\FollowupController;
use App\Http\Controllers\Lead\LeadAssignController;
use App\Http\Controllers\Lead\LeadController;
use App\Http\Controllers\Service\ServiceController;
use App\Http\Controllers\Setting\FollowupCategoryController;
use App\Http\Controllers\User\EducationController;
use App\Models\FollowupCategory;
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
    Route::resource('service', ServiceController::class);

    // Employee 
    Route::resource('employee', EmployeeController::class);
    Route::post('update-employee-designation',[EmployeeUpdateController::class,'updateDesignation']);
    Route::post('update-employee-reporting',[EmployeeUpdateController::class,'updateReporting']);

    // User update 
    Route::resource('education',EducationController::class);
    Route::resource('file',FileControllr::class);

    Route::resource('lead', LeadController::class);
    Route::get('lead-customer-profile/{id}',[LeadController::class,'profile']);
    Route::post('lead-assign-to',LeadAssignController::class);
    Route::resource('followup', FollowupController::class);

    // Customer  
    Route::get('clients',[CustomerController::class,'index']);

    // Setting 
    Route::resource('followup-category', FollowupCategoryController::class);
});


 
