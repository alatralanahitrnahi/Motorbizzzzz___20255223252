<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'current_stock')) {
                $table->decimal('current_stock', 10, 2)->default(0)->after('is_saleable');
            }
            if (!Schema::hasColumn('products', 'reorder_level')) {
                $table->decimal('reorder_level', 10, 2)->default(0)->after('current_stock');
            }
            if (!Schema::hasColumn('products', 'reserved_quantity')) {
                $table->decimal('reserved_quantity', 10, 2)->default(0)->after('reorder_level');
            }
            if (!Schema::hasColumn('products', 'product_type')) {
                $table->enum('product_type', ['finished_good', 'semi_finished', 'component', 'assembly'])->default('finished_good')->after('category');
            }
            if (!Schema::hasColumn('products', 'stock_status')) {
                $table->enum('stock_status', ['available', 'reserved', 'in_production', 'quality_hold'])->default('available')->after('reserved_quantity');
            }
            if (!Schema::hasColumn('products', 'location_id')) {
                $table->foreignId('location_id')->nullable()->after('stock_status')->constrained('inventory_locations')->onDelete('set null');
            }
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'location_id')) {
                $table->dropForeign(['location_id']);
                $table->dropColumn('location_id');
            }
            if (Schema::hasColumn('products', 'stock_status')) {
                $table->dropColumn('stock_status');
            }
            if (Schema::hasColumn('products', 'product_type')) {
                $table->dropColumn('product_type');
            }
            if (Schema::hasColumn('products', 'reserved_quantity')) {
                $table->dropColumn('reserved_quantity');
            }
            if (Schema::hasColumn('products', 'reorder_level')) {
                $table->dropColumn('reorder_level');
            }
            if (Schema::hasColumn('products', 'current_stock')) {
                $table->dropColumn('current_stock');
            }
        });
    }
};
