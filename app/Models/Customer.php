<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use HasFactory, SoftDeletes;  

    protected $fillable = [
        'user_id',
        'lead_source_id',
        'customer_id',
        'referred_by',
        'total_sales',
        'total_sales_amount',
        'newsletter_subscribed',
        'created_by',
        'updated_by',
        'deleted_by',
    ];
    

    public static function generateNextCustomerId(){
        $largest_user_id = Customer::where('customer_id', 'like', 'CUS-%')
        ->pluck('customer_id')
                ->map(function ($id) {
                        return preg_replace("/[^0-9]/", "", $id);
                }) 
        ->max();  
        $largest_user_id++; 
        $new_user_id = 'CUS-' . str_pad($largest_user_id, 6, '0', STR_PAD_LEFT);
        return $new_user_id;
    } 

    public function user()
    {
        return $this->belongsTo(User::class); 
    }
 
    public function leadSource()
    {
        return $this->belongsTo(LeadSource::class, 'lead_source_id'); 
    }
 
    public function referredBy()
    {
        return $this->belongsTo(User::class, 'referred_by');
    }
 
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
 
    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
 
    public function deletedBy()
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }
}
