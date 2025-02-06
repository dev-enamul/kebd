<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'guard_name',
    ];
 
    public function designations()
    {
        return $this->belongsToMany(Designation::class, 'designation_permissions');
    }

}
