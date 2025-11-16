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
        Schema::create('quality_checklist_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('quality_checklist_id');
            $table->string('item_name');
            $table->text('description')->nullable();
            $table->string('criteria_type'); // pass_fail, numeric, text
            $table->string('acceptable_criteria')->nullable();
            $table->decimal('min_value', 10, 2)->nullable();
            $table->decimal('max_value', 10, 2)->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_required')->default(true);
            $table->timestamps();
            
            $table->foreign('quality_checklist_id')->references('id')->on('quality_checklists')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quality_checklist_items');
    }
};