<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserEnglish extends Model
{
    use HasFactory; 

    protected $fillable = [
        'user_id', 
        'certificate_name', 
        'score',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }  
}
