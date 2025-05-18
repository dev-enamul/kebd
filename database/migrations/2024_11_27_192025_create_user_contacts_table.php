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
        Schema::create('user_contacts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained(); 
            $table->string('name')->nullable();
            $table->string('factory_name')->nullable();
            $table->string('role')->nullable();
            $table->string('phone', 20)->nullable(); 
            $table->string('email', 45)->nullable(); 
            $table->text('head_office_address')->nullable();
            $table->text('factory_address')->nullable();
            $table->text('remark')->nullable();
            
            $table->string('whatsapp', 20)->nullable();
            $table->string('imo', 20)->nullable();
            $table->string('facebook', 100)->nullable();
            $table->string('linkedin', 100)->nullable();
            $table->timestamps(); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_contacts');
    }
};

