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
        Schema::table('transaction_items', function (Blueprint $table) {
            // Change price_type column to allow custom value
            $table->enum('price_type', ['retail', 'wholesale', 'custom'])->change();

            // Make product_id nullable for custom products
            $table->unsignedBigInteger('product_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transaction_items', function (Blueprint $table) {
            // Revert back to original enum
            $table->enum('price_type', ['retail', 'wholesale'])->change();

            // Make product_id non-nullable again
            $table->unsignedBigInteger('product_id')->nullable(false)->change();
        });
    }
};
