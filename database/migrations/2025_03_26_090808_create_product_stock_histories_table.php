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
        Schema::create('product_stock_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_stock_id')->constrained('product_stocks')->onDelete('cascade');
            $table->enum('stock_type', ['home_stock', 'store_stock', 'pre_order_stock']);
            $table->enum('stock_activity', ['purchase', 'keep', 'pre_order', 'sales', 'transfer', 'add', 'retur', 'remove']);
            $table->enum('status', ['add', 'remove', 'change']);
            $table->integer('stock_before');
            $table->integer('stock_after');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_stock_histories');
    }
};
