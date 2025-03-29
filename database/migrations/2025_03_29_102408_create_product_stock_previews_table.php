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
        Schema::create('product_stock_previews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('size_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('color_id')->nullable()->constrained()->onDelete('cascade');
            $table->integer('all_stock')->nullable();
            $table->integer('home_stock')->nullable();
            $table->integer('store_stock')->nullable();
            $table->integer('pre_order_stock')->nullable();
            $table->integer('selling_price')->nullable();
            $table->integer('purchase_price')->nullable();
            $table->enum('status', ['ACTIVE', 'DEFAULT', 'ARCHIVE'])->default('DEFAULT');
            $table->json('error')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_stock_previews');
    }
};
