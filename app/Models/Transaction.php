<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory; 
    protected $fillable = [
        'bank_id',
        'transaction_type', // 1 = Deposit, 2 = Withdraw
        'amount',
        'currency',
        'reference_number',
        'description',
        'transaction_date',
        'status',
    ];
}
