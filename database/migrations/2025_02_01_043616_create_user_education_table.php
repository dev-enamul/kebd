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
        Schema::create('user_education', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->string('institution_name');  
            $table->string('degree')->nullable();          
            $table->string('field_of_study')->nullable();  
            $table->year('start_year')->nullable();        
            $table->year('end_year')->nullable(); 
            $table->string('certificate_path')->nullable();
            $table->boolean('is_last')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_education');
    }
};
