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
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('keep_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('customer_id')->constrained()->onDelete('cascade');
            $table->foreignId('term_of_payment_id')->constrained()->onDelete('cascade');
            $table->string('no_sale', 25);
            $table->enum('discount_type', ['persen', 'rupiah'])->nullable();
            $table->integer('discount')->nullable();
            $table->foreignId('discount_id')->nullable()->constrained()->onDelete('cascade');
            $table->integer('tax')->nullable();
            $table->integer('ship')->nullable();
            $table->integer('sub_total');
            $table->integer('total_price');
            $table->integer('total_items');
            $table->longText('desc')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};
