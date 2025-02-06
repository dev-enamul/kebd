<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\EducationStoreRequest;
use App\Models\UserEducation;
use Exception;
use Illuminate\Http\Request;
use Symfony\Component\ErrorHandler\ErrorEnhancer\UndefinedFunctionErrorEnhancer;

use function Laravel\Prompts\error;

class EducationController extends Controller
{
    public function index(Request $request){
        try{
            $user_id = $request->user_id;
            $is_last = $request->is_last;
            $education = UserEducation::where('user_id',$user_id)->where('is_last',$is_last)->get();
            return success_response($education);
        }catch(Exception $e){
            return error_response($e->getMessage());
        }

    }
    public function store(EducationStoreRequest $request){
        try{
            UserEducation::create([
                'user_id' => $request->user_id, 
                'institution_name' => $request->institution_name,
                'degree' =>$request->degree,
                'field_of_study' => $request->field_of_study,
                'start_year' => $request->start_year,
                'end_year' => $request->end_year,
                'is_last' => $request->is_last??false,
            ]);
            return success_response("Education Information Created Successfully!");
        }catch(Exception $e){
            return error_response($e->getMessage(),500);
        }
    }

    public function show($id){
        try{
            $education  = UserEducation::find($id);
            if(!$education){
                error_response("Education information not found", 404);
            }
            return success_response($education);
        }catch(Exception $e){
            return error_response($e->getMessage());
        }
    }

    public function update($id, Request $request){
        try{ 
            $education = UserEducation::find($id);
            if(!$education){
                error_response("Education information not found", 404);
            }
            $education->update([ 
                'institution_name' => $request->institution_name,
                'degree' =>$request->degree,
                'field_of_study' => $request->field_of_study,
                'start_year' => $request->start_year,
                'end_year' => $request->end_year,
                'is_last' => $request->is_last??false,
            ]);
            return success_response("Education Information Updated Successfully");
        }catch(Exception $e){
            return error_response($e->getMessage());
        }
    }

    public function destroy($id){
        try{
            $education = UserEducation::find($id);
            if(!$education){
                error_response("Education information not found", 404);
            }
            $education->delete();
            return success_response("Education information deleted");
        }catch(Exception $e){
            return error_response($e->getMessage());
        }
    }
}
