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

    public function salesPipelines()
    {
        return $this->belongsToMany(SalesPipeline::class, 'sales_pipeline_service', 'service_id', 'sales_pipeline_id');
    }

  
}
