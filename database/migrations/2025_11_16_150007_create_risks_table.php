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
        Schema::create('risks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('business_id');
            $table->unsignedBigInteger('risk_category_id')->nullable();
            $table->string('title');
            $table->text('description')->nullable();
            $table->text('cause')->nullable();
            $table->text('effect')->nullable();
            $table->unsignedBigInteger('owner_id')->nullable();
            $table->string('likelihood')->default('medium'); // low, medium, high
            $table->string('impact')->default('medium'); // low, medium, high
            $table->string('risk_level')->default('medium'); // low, medium, high, critical
            $table->string('status')->default('identified'); // identified, assessed, mitigated, monitored, closed
            $table->date('assessment_date')->nullable();
            $table->date('review_date')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->foreign('business_id')->references('id')->on('businesses')->onDelete('cascade');
            $table->foreign('risk_category_id')->references('id')->on('risk_categories')->onDelete('set null');
            $table->foreign('owner_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('risks');
    }
};