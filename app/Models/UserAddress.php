<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserAddress extends Model
{
    use HasFactory; 
    protected $fillable = [
        'user_id',
        'is_same_address',
        'address_type',
        'house_or_state',
        'village_or_area',
        'post_office',
        'upazila_thana',
        'district',
        'division',
    ];     

    public function user()
    {
        return $this->belongsTo(User::class);
    }
 
}
