<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FollowupCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'status',
        'serial',
    ];

    public function countFolowup(){
        return $this->hasMany(SalesPipeline::class,'followup_categorie_id')->count();
    }
}
