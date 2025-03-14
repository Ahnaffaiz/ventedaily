<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class ProductStockSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();

        // Ambil semua ID produk, ukuran, dan warna
        $productIds = DB::table('products')->pluck('id')->toArray();
        $sizeIds = DB::table('sizes')->pluck('id')->toArray(); // Pastikan tabel size ada
        $colorIds = DB::table('colors')->pluck('id')->toArray(); // Pastikan tabel color ada

        if (empty($productIds) || empty($sizeIds) || empty($colorIds)) {
            return; // Jika tidak ada produk, ukuran, atau warna, tidak perlu insert
        }

        foreach ($productIds as $productId) {
            // Setiap produk memiliki 3-5 stok
            foreach (range(1, rand(3, 5)) as $index) {
                $purchasePrice = $faker->randomFloat(2, 50_000, 200_000); // Harga beli 50K - 200K
                $sellingPrice = $purchasePrice + $faker->randomFloat(2, 10_000, 100_000); // Harga jual lebih tinggi 10K - 100K

                $homeStock = rand(5, 50);
                $storeStock = rand(5, 50);
                $perOrderStock = rand(5, 50);
                $allStock = $homeStock + $storeStock + $perOrderStock; // Perbaikan: Total stok = home + store

                DB::table('product_stocks')->insert([
                    'product_id'     => $productId,
                    'size_id'        => $faker->randomElement($sizeIds),
                    'color_id'       => $faker->randomElement($colorIds),
                    'all_stock'      => $allStock,  // Fix: all_stock adalah home + store + pre order
                    'home_stock'     => $homeStock,
                    'store_stock'    => $storeStock,
                    'pre_order_stock'=> $perOrderStock,
                    'qc_stock'       => 0,
                    'vermak_stock'   => 0,
                    'purchase_price' => $purchasePrice,
                    'selling_price'  => $sellingPrice, // Harga jual harus lebih tinggi dari harga beli
                ]);
            }
        }
    }
}
