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
        Schema::table('transfer_product_stocks', function (Blueprint $table) {
            $table->foreignId('keep_product_id')->nullable()->constrained('keep_products')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transfer_product_stocks', function (Blueprint $table) {
            $table->dropForeign(['keep_product_id']);
            $table->dropColumn('keep_product_id');
        });
    }
};
