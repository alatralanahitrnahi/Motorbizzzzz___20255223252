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
        Schema::create('risk_incidents', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('business_id');
            $table->unsignedBigInteger('risk_id')->nullable();
            $table->string('title');
            $table->text('description')->nullable();
            $table->dateTime('incident_date');
            $table->string('incident_type')->nullable();
            $table->unsignedBigInteger('reported_by')->nullable();
            $table->text('affected_areas')->nullable();
            $table->decimal('financial_loss', 15, 2)->nullable();
            $table->integer('affected_people')->nullable();
            $table->string('severity')->default('medium'); // low, medium, high, critical
            $table->string('status')->default('reported'); // reported, investigated, resolved, closed
            $table->text('immediate_actions')->nullable();
            $table->text('root_cause')->nullable();
            $table->text('corrective_actions')->nullable();
            $table->date('resolution_date')->nullable();
            $table->text('lessons_learned')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->foreign('business_id')->references('id')->on('businesses')->onDelete('cascade');
            $table->foreign('risk_id')->references('id')->on('risks')->onDelete('set null');
            $table->foreign('reported_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('risk_incidents');
    }
};