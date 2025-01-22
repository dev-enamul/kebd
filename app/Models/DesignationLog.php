<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DesignationLog extends Model
{
    use HasFactory;  
    protected $fillable = [
        'user_id',
        'employee_id',
        'designation_id',
        'start_date',
        'end_date',
    ];


    public function permissions(){
        return $this->hasMany(DesignationPermission::class, 'designation_id');
    }
}
