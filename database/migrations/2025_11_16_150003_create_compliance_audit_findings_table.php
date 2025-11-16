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
        Schema::create('compliance_audit_findings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('business_id');
            $table->unsignedBigInteger('compliance_audit_id');
            $table->unsignedBigInteger('compliance_requirement_id')->nullable();
            $table->text('description');
            $table->text('evidence')->nullable();
            $table->string('severity')->default('low'); // low, medium, high, critical
            $table->string('status')->default('open'); // open, in_progress, resolved, closed
            $table->unsignedBigInteger('assigned_to')->nullable();
            $table->date('due_date')->nullable();
            $table->text('corrective_action')->nullable();
            $table->date('resolution_date')->nullable();
            $table->text('resolution_notes')->nullable();
            $table->timestamps();
            
            $table->foreign('business_id')->references('id')->on('businesses')->onDelete('cascade');
            $table->foreign('compliance_audit_id')->references('id')->on('compliance_audits')->onDelete('cascade');
            $table->foreign('compliance_requirement_id')->references('id')->on('compliance_requirements')->onDelete('set null');
            $table->foreign('assigned_to')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('compliance_audit_findings');
    }
};