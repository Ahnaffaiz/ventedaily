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
        Schema::table('keeps', function (Blueprint $table) {
            $table->foreignId('marketplace_id')->nullable()->constrained('marketplaces')->onDelete('cascade')->after('keep_time');
            $table->string('order_id_marketplace', 255)->nullable()->after('marketplace_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('keeps', function (Blueprint $table) {
            $table->dropForeign(['marketplace_id']);
            $table->dropColumn(['marketplace_id', 'order_id_marketplace']);
        });
    }
};
