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
        Schema::create('risk_mitigation_strategies', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('business_id');
            $table->unsignedBigInteger('risk_id');
            $table->string('strategy_name');
            $table->text('description')->nullable();
            $table->text('actions')->nullable();
            $table->unsignedBigInteger('responsible_person_id')->nullable();
            $table->decimal('cost', 15, 2)->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->string('status')->default('planned'); // planned, in_progress, implemented, completed
            $table->decimal('effectiveness', 5, 2)->nullable(); // 0-100 scale
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->foreign('business_id')->references('id')->on('businesses')->onDelete('cascade');
            $table->foreign('risk_id')->references('id')->on('risks')->onDelete('cascade');
            $table->foreign('responsible_person_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('risk_mitigation_strategies');
    }
};