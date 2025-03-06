<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FollowupLog extends Model
{
    use HasFactory; 

    protected $fillable = [
        'user_id',
        'customer_id',
        'pipeline_id',
        'followup_categorie_id',
        'purchase_probability',
        'price',
        'next_followup_date',
        'notes',
        'created_by',
        'updated_by',
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function followupBy(){
        return $this->belongsTo(User::class,'created_by');
    }

    public function followupCategory()
    {
        return $this->belongsTo(FollowupCategory::class, 'followup_categorie_id');
    }
    
    
}
