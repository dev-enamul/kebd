<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bank extends Model
{
    use HasFactory; 
    protected $fillable = [
        'name',
        'account_number',
        'balance',
        'branch',
        'account_holder',
        'swift_code',
        'iban',
        'currency',
        'contact_number',
        'email',
        'address',
        'status',
    ];
}
