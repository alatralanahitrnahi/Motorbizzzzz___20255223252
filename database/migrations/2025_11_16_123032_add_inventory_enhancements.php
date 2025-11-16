<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('materials', function (Blueprint $table) {
            $table->enum('material_type', ['raw_material', 'component', 'consumable', 'spare_part'])->default('raw_material')->after('category');
            $table->decimal('reserved_quantity', 10, 2)->default(0)->after('current_stock');
            $table->enum('stock_status', ['available', 'reserved', 'quarantine', 'low_stock'])->default('available')->after('reserved_quantity');
            $table->foreignId('location_id')->nullable()->after('stock_status')->constrained('inventory_locations')->onDelete('set null');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->enum('product_type', ['finished_good', 'semi_finished', 'component', 'assembly'])->default('finished_good')->after('category');
            $table->decimal('reserved_quantity', 10, 2)->default(0)->after('current_stock');
            $table->enum('stock_status', ['available', 'reserved', 'in_production', 'quality_hold'])->default('available')->after('reserved_quantity');
            $table->foreignId('location_id')->nullable()->after('stock_status')->constrained('inventory_locations')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('materials', function (Blueprint $table) {
            $table->dropForeign(['location_id']);
            $table->dropColumn(['material_type', 'reserved_quantity', 'stock_status', 'location_id']);
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['location_id']);
            $table->dropColumn(['product_type', 'reserved_quantity', 'stock_status', 'location_id']);
        });
    }
};
