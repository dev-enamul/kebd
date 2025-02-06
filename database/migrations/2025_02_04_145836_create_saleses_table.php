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
        Schema::create('saleses', function (Blueprint $table) {
            $table->id(); 
            $table->foreignId('user_id')->constrained('users'); 
            $table->foreignId('sales_pipeline_id')->nullable()->constrained('sales_pipelines'); 
            $table->foreignId('sales_by_user_id')->constrained('users'); 
            $table->decimal('price', 10, 2)->default(0); 
            $table->decimal('paid', 10, 2)->default(0); 
            $table->decimal('payment_schedule_amount', 10, 2)->default(0); 
            $table->boolean('is_paid')->default(false); 
            $table->boolean('is_deliveried')->default(false); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('saleses');
    }
};
