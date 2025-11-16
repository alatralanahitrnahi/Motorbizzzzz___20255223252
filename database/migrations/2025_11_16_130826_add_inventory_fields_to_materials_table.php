<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('materials', function (Blueprint $table) {
            if (!Schema::hasColumn('materials', 'current_stock')) {
                $table->decimal('current_stock', 10, 2)->default(0)->after('is_active');
            }
            if (!Schema::hasColumn('materials', 'reorder_level')) {
                $table->decimal('reorder_level', 10, 2)->default(0)->after('current_stock');
            }
            if (!Schema::hasColumn('materials', 'reserved_quantity')) {
                $table->decimal('reserved_quantity', 10, 2)->default(0)->after('reorder_level');
            }
            if (!Schema::hasColumn('materials', 'material_type')) {
                $table->enum('material_type', ['raw_material', 'component', 'consumable', 'spare_part'])->default('raw_material')->after('category');
            }
            if (!Schema::hasColumn('materials', 'stock_status')) {
                $table->enum('stock_status', ['available', 'reserved', 'quarantine', 'low_stock'])->default('available')->after('reserved_quantity');
            }
            if (!Schema::hasColumn('materials', 'location_id')) {
                $table->foreignId('location_id')->nullable()->after('stock_status')->constrained('inventory_locations')->onDelete('set null');
            }
        });
    }

    public function down(): void
    {
        Schema::table('materials', function (Blueprint $table) {
            if (Schema::hasColumn('materials', 'location_id')) {
                $table->dropForeign(['location_id']);
                $table->dropColumn('location_id');
            }
            if (Schema::hasColumn('materials', 'stock_status')) {
                $table->dropColumn('stock_status');
            }
            if (Schema::hasColumn('materials', 'material_type')) {
                $table->dropColumn('material_type');
            }
            if (Schema::hasColumn('materials', 'reserved_quantity')) {
                $table->dropColumn('reserved_quantity');
            }
            if (Schema::hasColumn('materials', 'reorder_level')) {
                $table->dropColumn('reorder_level');
            }
            if (Schema::hasColumn('materials', 'current_stock')) {
                $table->dropColumn('current_stock');
            }
        });
    }
};
