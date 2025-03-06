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
        Schema::create('keep_products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('keep_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_stock_id')->constrained()->onDelete('cascade');
            $table->integer('total_items');
            $table->integer('home_stock');
            $table->integer('store_stock');
            $table->integer('selling_price');
            $table->integer('purchase_price');
            $table->integer('total_price');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('keep_products');
    }
};
