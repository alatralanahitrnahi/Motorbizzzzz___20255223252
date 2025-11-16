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
        Schema::create('compliance_documents', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('business_id');
            $table->unsignedBigInteger('compliance_requirement_id')->nullable();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('document_type')->nullable(); // e.g., 'policy', 'procedure', 'record', 'certificate'
            $table->string('file_path')->nullable();
            $table->string('file_name')->nullable();
            $table->string('file_type')->nullable();
            $table->bigInteger('file_size')->nullable();
            $table->integer('version')->default(1);
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->date('approval_date')->nullable();
            $table->date('effective_date')->nullable();
            $table->date('review_date')->nullable();
            $table->string('status')->default('draft'); // draft, approved, archived
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->foreign('business_id')->references('id')->on('businesses')->onDelete('cascade');
            $table->foreign('compliance_requirement_id')->references('id')->on('compliance_requirements')->onDelete('set null');
            $table->foreign('approved_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('compliance_documents');
    }
};