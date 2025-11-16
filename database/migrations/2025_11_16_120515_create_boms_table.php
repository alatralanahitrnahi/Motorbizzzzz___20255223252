<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('boms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->string('version')->default('1.0');
            $table->decimal('quantity', 10, 2)->default(1);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index(['business_id', 'product_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('boms');
    }
};
