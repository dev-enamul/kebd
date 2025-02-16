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
        Schema::create('banks', function (Blueprint $table) {
            $table->id();
            $table->string('name');  
            $table->string('account_number'); 
            $table->decimal('balance', 15, 2)->default(0.00); 
            $table->string('branch')->nullable();   
            $table->string('account_holder')->nullable(); 
            $table->string('swift_code')->nullable();  
            $table->string('iban')->nullable();  
            $table->string('currency', 10)->default('BDT');  
            $table->string('contact_number')->nullable(); 
            $table->string('email')->nullable(); 
            $table->text('address')->nullable(); 
            $table->boolean('status')->default(true);
            $table->timestamps(); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('banks');
    }
};
