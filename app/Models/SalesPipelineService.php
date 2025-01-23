<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesPipelineService extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'customer_id',
        'sales_pipeline_id',
        'service_id',
    ];
}
