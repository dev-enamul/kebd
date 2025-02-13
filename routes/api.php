<?php

use App\Http\Controllers\Admin\Employee\DesignationController;
use App\Http\Controllers\Admin\Employee\EmployeeController;
use App\Http\Controllers\Admin\Employee\EmployeeUpdateController;
use App\Http\Controllers\Auth\AuthController; 
use App\Http\Controllers\Common\CountryApiController;
use App\Http\Controllers\Common\DesignationPermissionController;
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
use App\Http\Controllers\Payment\PaymentController;
use App\Http\Controllers\Payment\PaymentScheduleController;
use App\Http\Controllers\Salese\SaleseController;
use App\Http\Controllers\Service\ServiceController;
use App\Http\Controllers\Setting\FollowupCategoryController;
use App\Http\Controllers\User\EducationController;
use App\Http\Controllers\User\UserAddressController;
use App\Http\Controllers\User\UserContactController;
use App\Http\Controllers\User\UserEnglishController;
use App\Http\Controllers\User\UserUpdateController;
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
    Route::resource('designation-permission',DesignationPermissionController::class);
    Route::resource('service', ServiceController::class);

    // Employee 
    Route::resource('employee', EmployeeController::class);
    Route::post('update-employee-designation',[EmployeeUpdateController::class,'updateDesignation']);
    Route::post('update-employee-reporting',[EmployeeUpdateController::class,'updateReporting']);

    // User update 
    Route::resource('education',EducationController::class);
    Route::resource('file',FileControllr::class);
    Route::resource('english-score',UserEnglishController::class);
    Route::post('profile-picture-update',[UserUpdateController::class,"update_profile_picture"]);
    Route::put('bio-update',[UserUpdateController::class,"bio_update"]);

    // address 
    Route::post('address-update',[UserAddressController::class,'update']);
    Route::get('address/{user_id}',[UserAddressController::class,'show']); 
    // contact   
    Route::get('contact-data/{user_id}',[UserContactController::class,'contact_data']);
    Route::post('upate-contact',[UserContactController::class,'update_contact']);
    // Route::post('add-contact',[UserContactController::class,'add_contact']); 
    // Route::get('show-contact',[UserContactController::class,'show_contact']);

    Route::resource('lead', LeadController::class);
    Route::get('lead-customer-profile/{id}',[LeadController::class,'profile']);
   
    Route::post('lead-assign-to',LeadAssignController::class);
    Route::resource('followup', FollowupController::class);

    // Customer 
    Route::get('clients',[CustomerController::class,'index']); 

    // Salese 
    Route::resource('sales',SaleseController::class);
    Route::resource('payment-schedule',PaymentScheduleController::class);
    Route::post('pay-now',[PaymentController::class,'payNow']);
 

    // Setting 
    Route::resource('followup-category', FollowupCategoryController::class);
});


 
