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
        Schema::create('cart_items', function (Blueprint $table) {
            $table->id();
            $table->string('session_id'); // Track cart by session
            $table->foreignId('product_id')->nullable()->constrained()->onDelete('cascade'); // Null for custom products
            $table->string('product_name'); // Name for both regular and custom products
            $table->decimal('price', 10, 2); // Price per unit
            $table->enum('price_type', ['retail', 'wholesale', 'custom'])->default('custom');
            $table->integer('quantity')->default(1);
            $table->boolean('is_custom')->default(false); // True for manually added products
            $table->timestamps();

            $table->index(['session_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cart_items');
    }
};
