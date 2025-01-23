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
        'followup_categorie_id',
        'purchase_probability',
        'price',
        'next_followup_date',
        'last_contacted_at',
        'assigned_to',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
 
    public function services()
    {
        return $this->belongsToMany(Service::class, 'sales_pipeline_services', 'sales_pipeline_id', 'service_id');
    }
 
    public function followupCategory()
    {
        return $this->belongsTo(FollowupCategory::class, 'followup_categorie_id');
    }
}
