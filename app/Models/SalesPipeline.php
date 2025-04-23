<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesPipeline extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'customer_id',
        'service_id',
        "service_details",
        'qty',
        'followup_categorie_id',
        'purchase_probability',
        'price',
        'type',
        'next_followup_date',
        'last_contacted_at',
        'assigned_to',
        'status',
    ];
    

    public function user()
    {
        return $this->belongsTo(User::class,'user_id');
    }

    public function assignTo()
    {
        return $this->belongsTo(User::class,'assigned_to');
    }

    public function follwoup()
    {
        return $this->belongsTo(FollowupLog::class,'pipeline_id');
    }
 
    public function service()
    {
        return $this->belongsTo(Service::class,'service_id');
    }
 
    public function followupCategory()
    {
        return $this->belongsTo(FollowupCategory::class, 'followup_categorie_id');
    }


}
