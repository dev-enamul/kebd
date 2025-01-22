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
        'service_ids',
        'followup_categorie_id',
        'purchase_probability',
        'price',
        'next_followup_date',
        'last_contacted_at',
        'assigned_to',
        'status',
    ];
    
}
