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
        Schema::create('payment_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); 
            $table->foreignId('salese_id')->constrained('saleses')->onDelete('cascade'); 
            $table->date('date');
            $table->decimal('amount', 10, 2); 
            $table->decimal('paid_amount', 10, 2)->default(0); 
            $table->integer('status')->default(0)->comment('0=Unpaid, 1=Paid, 2=Partial'); 
            $table->timestamps();
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_schedules');
    }
};
