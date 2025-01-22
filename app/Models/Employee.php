<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Employee extends Model
{
    use HasFactory; 
    protected $fillable = [
        'user_id',
        'employee_id',
        'designation_id',
        'referred_by',
        'signature',
        'salary',
        'status',
        'resigned_at',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    public static function generateNextEmployeeId(){ 
        $largest_employee_id = Employee::where('employee_id', 'like', 'EMP-%') 
        ->pluck('employee_id')
                ->map(function ($id) {
                        return preg_replace("/[^0-9]/", "", $id);
                }) 
        ->max(); 
        $largest_employee_id++;
        $new_employee_id = 'EMP-' . str_pad($largest_employee_id, 6, '0', STR_PAD_LEFT);
        return $new_employee_id;
    } 

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    

    public function designation()
    {
        return $this->belongsTo(Designation::class);
    }

    public function designationLog()
    {
        return $this->hasMany(DesignationLog::class);
    }
    
}
