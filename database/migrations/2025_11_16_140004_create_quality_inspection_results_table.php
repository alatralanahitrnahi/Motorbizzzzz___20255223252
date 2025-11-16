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
        Schema::create('quality_inspection_results', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('quality_inspection_id');
            $table->unsignedBigInteger('quality_checklist_item_id');
            $table->string('result_value')->nullable();
            $table->boolean('passed')->nullable();
            $table->text('remarks')->nullable();
            $table->string('attachment_path')->nullable();
            $table->timestamps();
            
            $table->foreign('quality_inspection_id')->references('id')->on('quality_inspections')->onDelete('cascade');
            $table->foreign('quality_checklist_item_id')->references('id')->on('quality_checklist_items');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quality_inspection_results');
    }
};