<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_batches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->string('batch_number')->unique();
            $table->decimal('quantity', 10, 2);
            $table->decimal('quantity_available', 10, 2);
            $table->date('manufactured_date');
            $table->date('expiry_date')->nullable();
            $table->foreignId('work_order_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('location_id')->nullable()->constrained('inventory_locations')->onDelete('set null');
            $table->enum('status', ['available', 'reserved', 'shipped', 'expired', 'quarantine'])->default('available');
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->index(['business_id', 'product_id', 'status']);
            $table->index('batch_number');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_batches');
    }
};
