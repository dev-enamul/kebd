<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',     
        'salese_id',   
        'date',        
        'amount',      
        'paid_amount', 
        'status',      
    ];
 
    public function user()
    {
        return $this->belongsTo(User::class);
    }
 
    public function sale()
    {
        return $this->belongsTo(Salese::class, 'salese_id');
    }
 
    public function isFullyPaid()
    {
        return $this->status === 3; 
    }
 
    public function isPartiallyPaid()
    {
        return $this->status === 2; 
    }
 
    public function isUnpaid()
    {
        return $this->status === 1; 
    }

}
