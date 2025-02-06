<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DesignationPermission extends Model
{
    use HasFactory;

    protected $fillable = [
        'designation_id',
        'permission_id' 
    ];


    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'designation_permissions');
    }
}
