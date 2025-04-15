<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Tambahkan kolom sementara untuk backup data
        Schema::table('transfer_product_stocks', function (Blueprint $table) {
            $table->unsignedBigInteger('keep_product_id_backup')->nullable();
        });

        // 2. Salin isi kolom lama ke kolom backup
        DB::statement('UPDATE transfer_product_stocks SET keep_product_id_backup = keep_product_id');

        // 3. Drop foreign key dan kolom lama
        Schema::table('transfer_product_stocks', function (Blueprint $table) {
            $table->dropForeign(['keep_product_id']);
            $table->dropColumn('keep_product_id');
        });

        // 4. Tambahkan kolom baru sebagai JSON
        Schema::table('transfer_product_stocks', function (Blueprint $table) {
            $table->json('keep_product_id')->nullable();
        });

        // 5. Isi kolom JSON dengan data dari kolom backup (dalam array satu elemen)
        DB::table('transfer_product_stocks')->whereNotNull('keep_product_id_backup')->get()->each(function ($row) {
            DB::table('transfer_product_stocks')
                ->where('id', $row->id)
                ->update(['keep_product_id' => json_encode([$row->keep_product_id_backup])]);
        });

        // 6. Hapus kolom backup
        Schema::table('transfer_product_stocks', function (Blueprint $table) {
            $table->dropColumn('keep_product_id_backup');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Jika rollback, kita hanya bisa kembalikan kolom json ke unsignedBigInteger (tanpa restore nilai asli yang presisi)
        Schema::table('transfer_product_stocks', function (Blueprint $table) {
            $table->dropColumn('keep_product_id');
            $table->foreignId('keep_product_id')->nullable()->constrained('keep_products')->onDelete('cascade');
        });
    }
};
