<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Designation extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'status',
    ];

    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'designation_permissions');
    }
}
