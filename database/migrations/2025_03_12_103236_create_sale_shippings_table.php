<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('sale_shippings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sale_id')->constrained('sales')->onDelete('cascade');
            $table->enum('status', ['siap kirim', 'expedisi']);
            $table->dateTime('date');
            $table->integer('cost');
            $table->string('no_resi', 255);
            $table->string('city', 255);
            $table->foreignId('marketplace_id')->constrained('marketplaces')->onDelete('cascade');
            $table->string('order_id_marketplace', 255);
            $table->string('customer_name', 255);
            $table->string('address', 255);
            $table->string('phone', 25);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sale_shippings');
    }
};
