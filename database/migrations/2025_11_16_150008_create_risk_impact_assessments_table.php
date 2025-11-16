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
        Schema::create('risk_impact_assessments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('business_id');
            $table->unsignedBigInteger('risk_id');
            $table->decimal('financial_impact', 15, 2)->nullable();
            $table->decimal('operational_impact', 5, 2)->nullable();
            $table->decimal('reputational_impact', 5, 2)->nullable();
            $table->decimal('legal_impact', 5, 2)->nullable();
            $table->decimal('safety_impact', 5, 2)->nullable();
            $table->text('assessment_details')->nullable();
            $table->unsignedBigInteger('assessed_by')->nullable();
            $table->date('assessment_date')->nullable();
            $table->text('methodology')->nullable();
            $table->timestamps();
            
            $table->foreign('business_id')->references('id')->on('businesses')->onDelete('cascade');
            $table->foreign('risk_id')->references('id')->on('risks')->onDelete('cascade');
            $table->foreign('assessed_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('risk_impact_assessments');
    }
};