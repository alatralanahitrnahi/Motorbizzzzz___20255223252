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
        Schema::create('business_continuity_plans', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('business_id');
            $table->string('title');
            $table->text('description')->nullable();
            $table->text('scope')->nullable();
            $table->text('objectives')->nullable();
            $table->unsignedBigInteger('owner_id')->nullable();
            $table->text('critical_functions')->nullable();
            $table->text('recovery_strategies')->nullable();
            $table->text('resource_requirements')->nullable();
            $table->text('contact_information')->nullable();
            $table->text('communication_plan')->nullable();
            $table->date('last_tested_date')->nullable();
            $table->date('next_test_date')->nullable();
            $table->string('status')->default('active'); // active, inactive, testing, review_required
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->foreign('business_id')->references('id')->on('businesses')->onDelete('cascade');
            $table->foreign('owner_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('business_continuity_plans');
    }
};