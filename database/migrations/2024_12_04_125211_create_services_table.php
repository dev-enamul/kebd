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
        Schema::create('services', function (Blueprint $table) { 
            $table->id(); 
            $table->string('title'); 
            $table->string('slug')->unique(); 
            $table->text('description')->nullable();
            $table->decimal('regular_price', 10, 2); 
            $table->decimal('sell_price', 10, 2); 
             
            $table->unsignedInteger('status')->default(1)->comment("1=Active, 0= UnActive");
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
