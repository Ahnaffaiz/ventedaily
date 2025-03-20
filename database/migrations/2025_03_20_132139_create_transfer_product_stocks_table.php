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
        Schema::create('transfer_product_stocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transfer_stock_id')->constrained('transfer_stocks')->onDelete('cascade');
            $table->foreignId('product_stock_id')->constrained('product_stocks')->onDelete('cascade');
            $table->integer('stock');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transfer_product_stocks');
    }
};
