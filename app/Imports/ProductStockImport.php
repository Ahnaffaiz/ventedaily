<?php

namespace App\Imports;

use App\Enums\ProductStatus;
use App\Models\Color;
use App\Models\Product;
use App\Models\ProductStockPreview;
use App\Models\Size;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ProductStockImport implements ToCollection, WithHeadingRow
{
    /**
    * @param Collection $collection
    */
    public function collection(Collection $rows)
    {
        foreach ($rows as $key => $row) {
            if (!array_filter($row->toArray())) {
                return null;
            }
            $error = [];

            //check product
            $product_id = Product::where('name', strtolower($row['name']))->first();
            if($product_id == null) {
                array_push($error,'Produk Tidak Ditemukan');
            }

            //check product
            $color_id = Color::where('name', strtolower($row['color']))->first();
            if($color_id == null) {
                array_push($error,'Warna Tidak Ditemukan');
            }

            //check size
            $size_id = Size::where('name', strtolower($row['size']))->first();
            if($size_id == null) {
                array_push($error,'Size Tidak Ditemukan');
            }

            //check status
            if (!in_array($row['status'], ProductStatus::getValues())) {
                array_push($error,'Silahkan Periksa Status Product');
                $row['status'] = 'DEFAULT';
            }

            if (!ctype_digit((int) $row['purchase_price'])) {
                array_push($error,'Purchase Price harus angka');
                $row['purchase_price'] = 0;
            }

            if (!ctype_digit((int) $row['selling_price'])) {
                array_push($error,'Selling Price harus angka');
                $row['selling_price'] = 0;
            }

            if (!is_int((int) $row['home_stock'])) {
                array_push($error,'Home Stock harus angka');
                $row['home_stock'] = 0;
            }

            if (!is_int((int) $row['store_stock'])) {
                array_push($error,'Store Stock harus angka');
                $row['store_stock'] = 0;
            }

            if (!is_int((int) $row['pre_order_stock'])) {
                array_push($error,'Pre Order Stock harus angka');
                $row['pre_order_stock'] = 0;
            }

            ProductStockPreview::firstOrCreate([
                'product_id' => $product_id ? $product_id->id : null,
                'color_id' => $color_id ? $color_id->id : null,
                'size_id' => $size_id ? $size_id->id : null,
            ], [
                'status' => $row['status'],
                'purchase_price' => $row['purchase_price'],
                'selling_price' => $row['selling_price'],
                'home_stock' => $row['home_stock'],
                'store_stock' => $row['store_stock'],
                'pre_order_stock' => $row['pre_order_stock'],
                'all_stock' => $row['home_stock'] + $row['store_stock'] + $row['pre_order_stock'],
                'error' => $error,
            ]);
        }
    }
}
