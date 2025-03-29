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
        Schema::create('product_previews', function (Blueprint $table) {
            $table->id();
            $table->string('name', 25)->nullable();
            $table->string('image')->nullable();
            $table->string('imei', 225)->nullable();
            $table->string('code', 225)->nullable();
            $table->boolean('is_favorite')->default(false);
            $table->enum('discount_type', ['%', 'rupiah'])->nullable();
            $table->enum('status', ['ACTIVE', 'DEFAULT', 'ARCHIVE'])->default('DEFAULT');
            $table->foreignId('category_id')->nullable()->constrained()->onDelete('cascade');
            $table->longText('desc')->nullable();
            $table->json('error')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_previews');
    }
};
