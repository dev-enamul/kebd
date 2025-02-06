<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Salese extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'sales_pipeline_id',
        'sales_by_user_id',
        'price',
        'paid',
        'payment_schedule_amount',
        'is_paid',
        'is_deliveried',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
 
    public function salesPipeline()
    {
        return $this->belongsTo(SalesPipeline::class);
    } 
    public function salesByUser()
    {
        return $this->belongsTo(User::class, 'sales_by_user_id');
    }

} 
