<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserContact extends Model
{
    use HasFactory; 
    protected $fillable = [
        'user_id',
        'name',
        'factory_name',
        'role',
        'phone',
        'email',
        'head_office_address',
        'factory_address',
        'remark',
        'whatsapp',
        'imo',
        'facebook',
        'linkedin',
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
}
