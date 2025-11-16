<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('work_order_operations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('work_order_id')->constrained()->onDelete('cascade');
            $table->string('operation_name');
            $table->foreignId('machine_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('operator_id')->nullable()->constrained('users')->onDelete('set null');
            $table->integer('sequence')->default(1);
            $table->integer('planned_duration')->default(0);
            $table->integer('actual_duration')->nullable();
            $table->enum('status', ['pending', 'in_progress', 'completed'])->default('pending');
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->index(['work_order_id', 'sequence']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('work_order_operations');
    }
};
