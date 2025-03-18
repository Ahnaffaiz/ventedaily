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
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255)->nullable();
            $table->string('address', 255)->nullable();
            $table->string('telp', 25)->nullable();
            $table->string('owner', 25)->nullable();
            $table->time('keep_timeout')->nullable();
            $table->string('keep_code');
            $table->string('keep_increment');
            $table->string('pre_order_code');
            $table->string('pre_order_increment');
            $table->string('sale_code');
            $table->string('sale_increment');
            $table->string('logo', 255)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
