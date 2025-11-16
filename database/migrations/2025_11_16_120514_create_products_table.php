<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained()->onDelete('cascade');
            $table->string('product_code')->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('category')->nullable();
            $table->string('unit');
            $table->decimal('selling_price', 10, 2);
            $table->decimal('cost_price', 10, 2)->nullable();
            $table->integer('reorder_level')->default(0);
            $table->integer('current_stock')->default(0);
            $table->foreignId('bom_id')->nullable()->constrained('boms')->onDelete('set null');
            $table->integer('manufacturing_time')->default(0);
            $table->boolean('is_manufactured')->default(true);
            $table->boolean('is_saleable')->default(true);
            $table->json('images')->nullable();
            $table->timestamps();
            
            $table->index(['business_id', 'is_saleable']);
            $table->index('product_code');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
