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
        Schema::create('product_stocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->foreignId('size_id')->constrained()->onDelete('cascade');
            $table->foreignId('color_id')->constrained()->onDelete('cascade');
            $table->integer('all_stock');
            $table->integer('home_stock');
            $table->integer('store_stock');
            $table->integer('pre_order_stock');
            $table->integer('qc_stock');
            $table->integer('vermak_stock');
            $table->integer('selling_price');
            $table->integer('purchase_price');
            $table->enum('status', ['ACTIVE', 'DEFAULT', 'ARCHIVE'])->default('DEFAULT');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_stocks');
    }
};
