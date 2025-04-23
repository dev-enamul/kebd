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
        Schema::create('sales_pipelines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('customer_id')->constrained('customers')->onDelete('cascade');
            $table->foreignId('service_id')->constrained('services');
            $table->string('service_details')->nullable();
            $table->integer('qty')->nullable()->default(1);
            $table->foreignId('followup_categorie_id')->constrained('followup_categories'); 
            $table->integer('purchase_probability')->nullable()->default(null)->comment("0-100%");
            $table->decimal('price')->nullable()->default(null);
            $table->date('next_followup_date')->nullable();
            $table->timestamp('last_contacted_at')->nullable();
            
            $table->foreignId('assigned_to')->nullable()->constrained('users')->onDelete('set null'); 
            $table->enum('status', ['Active', 'Rejected', 'Salsed', 'Waiting'])->default('Active');
            $table->enum('type', ['customer_data', 'lead_data']);
            $table->timestamps(); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leads');
    } 
};
