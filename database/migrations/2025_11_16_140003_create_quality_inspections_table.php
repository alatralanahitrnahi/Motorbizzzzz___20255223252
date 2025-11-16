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
        Schema::create('quality_inspections', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('business_id');
            $table->unsignedBigInteger('quality_checklist_id');
            $table->unsignedBigInteger('inspector_id');
            $table->unsignedBigInteger('reference_id')->nullable(); // Could be material_id, product_id, work_order_id, etc.
            $table->string('reference_type')->nullable(); // material, product, work_order, etc.
            $table->string('inspection_type'); // incoming, in_process, final
            $table->string('batch_number')->nullable();
            $table->dateTime('inspection_date');
            $table->string('status'); // pending, in_progress, completed, rejected
            $table->text('notes')->nullable();
            $table->decimal('overall_score', 5, 2)->nullable();
            $table->boolean('passed')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
            
            $table->foreign('business_id')->references('id')->on('businesses')->onDelete('cascade');
            $table->foreign('quality_checklist_id')->references('id')->on('quality_checklists');
            $table->foreign('inspector_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quality_inspections');
    }
};