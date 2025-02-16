<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bank_id')->constrained()->onDelete('cascade');  
            $table->integer('transaction_type'); //1=Deposit, 2=Withdraw
            $table->decimal('amount', 15, 2);  
            $table->string('currency', 10)->default('BDT'); 
            $table->string('reference_number')->nullable();
            $table->text('description')->nullable();
            $table->dateTime('transaction_date')->default(now());  
            $table->boolean('status')->default(true); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
