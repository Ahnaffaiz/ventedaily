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
            $table->enum('stock_activity', ['purchase', 'keep', 'pre order', 'sales', 'transfer', 'stock in' ,'add', 'retur', 'remove', 'import']);
            $table->enum('status', ['add', 'remove', 'change', 'change remove', 'change add']);
            $table->enum('from_stock_type', ['home_stock', 'store_stock', 'pre_order_stock'])->nullable();
            $table->enum('to_stock_type', ['home_stock', 'store_stock', 'pre_order_stock'])->nullable();
            $table->string('reference')->nullable();
            $table->integer('qty');
            $table->integer('final_all_stock')->default(0);
            $table->integer('final_home_stock')->default(0);
            $table->integer('final_store_stock')->default(0);
            $table->integer('final_pre_order_stock')->default(0);
            $table->integer('is_temporary')->default(false);
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
