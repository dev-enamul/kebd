<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Service extends Model
{
    use HasFactory, SoftDeletes; 

    protected $fillable = [
        'title',
        'slug',
        'description',
        'regular_price',
        'sell_price',
        'status',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

  
}
