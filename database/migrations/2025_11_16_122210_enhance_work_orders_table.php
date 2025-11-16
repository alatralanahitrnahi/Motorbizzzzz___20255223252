<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('work_orders', function (Blueprint $table) {
            $table->foreignId('sales_order_id')->nullable()->after('business_id')->constrained()->onDelete('set null');
            $table->string('work_order_number')->unique()->after('sales_order_id');
            $table->foreignId('product_id')->nullable()->after('work_order_number')->constrained()->onDelete('cascade');
            $table->decimal('quantity_planned', 10, 2)->default(0)->after('product_id');
            $table->decimal('quantity_produced', 10, 2)->default(0)->after('quantity_planned');
            $table->decimal('quantity_rejected', 10, 2)->default(0)->after('quantity_produced');
            $table->foreignId('assigned_to')->nullable()->after('machine_id')->constrained('users')->onDelete('set null');
            $table->date('start_date')->nullable()->after('assigned_to');
            $table->date('end_date')->nullable()->after('start_date');
            $table->enum('priority', ['low', 'medium', 'high'])->default('medium')->after('end_date');
            $table->timestamp('actual_start_time')->nullable()->after('priority');
            $table->timestamp('actual_end_time')->nullable()->after('actual_start_time');
        });
    }

    public function down(): void
    {
        Schema::table('work_orders', function (Blueprint $table) {
            $table->dropForeign(['sales_order_id']);
            $table->dropForeign(['product_id']);
            $table->dropForeign(['assigned_to']);
            $table->dropColumn([
                'sales_order_id', 'work_order_number', 'product_id',
                'quantity_planned', 'quantity_produced', 'quantity_rejected',
                'assigned_to', 'start_date', 'end_date', 'priority',
                'actual_start_time', 'actual_end_time'
            ]);
        });
    }
};
