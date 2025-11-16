<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stock_movements', function (Blueprint $table) {
            $table->foreignId('from_location_id')->nullable()->after('from_location')->constrained('inventory_locations')->onDelete('set null');
            $table->foreignId('to_location_id')->nullable()->after('to_location')->constrained('inventory_locations')->onDelete('set null');
            $table->string('batch_number')->nullable()->after('to_location_id');
            $table->decimal('unit_cost', 10, 2)->nullable()->after('quantity');
            $table->decimal('total_cost', 12, 2)->nullable()->after('unit_cost');
            $table->decimal('running_balance', 10, 2)->nullable()->after('total_cost');
        });
    }

    public function down(): void
    {
        Schema::table('stock_movements', function (Blueprint $table) {
            $table->dropForeign(['from_location_id']);
            $table->dropForeign(['to_location_id']);
            $table->dropColumn(['from_location_id', 'to_location_id', 'batch_number', 'unit_cost', 'total_cost', 'running_balance']);
        });
    }
};
