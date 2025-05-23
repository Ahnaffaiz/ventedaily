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
        Schema::create('sale_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('sale_id')->constrained()->onDelete('cascade');
            $table->dateTime('date');
            $table->string('reference', 225);
            $table->integer('amount');
            $table->integer('cash_received')->nullable();
            $table->integer('cash_change')->nullable();
            $table->enum('payment_type', ['cash', 'transfer']);
            $table->foreignId('bank_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('account_number', 255)->nullable();
            $table->string('account_name', 225)->nullable();
            $table->longText('desc')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sale_payments');
    }
};
