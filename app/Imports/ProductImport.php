<?php

namespace App\Imports;

use App\Enums\ProductStatus;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductPreview;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Str;

class ProductImport implements ToCollection, WithHeadingRow
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
            if($row['name'] == null) {
                array_push($error, 'Nama Dibutuhkan');
            }

            //check category
            $category_id = Category::where('name', strtolower($row['category']))->first();
            if($category_id == null) {
                array_push($error,'Kategori Tidak Ditemukan');
            }
            //check status
            if (!in_array($row['status'], ProductStatus::getValues())) {
                $row['status'] = 'DEFAULT';
            }
            //check favorite
            if (!in_array(strtolower(strtolower($row['favorite'])), ['yes', 'no'])) {
                $row['favorite'] = false;
            }

            //imei
            do {
                $imei = Str::random(20);
            } while (Product::where('imei', $imei)->exists() || ProductPreview::where('imei', $imei)->exists());

            do {
                $code = Str::random(10);
            } while (Product::where('code', $code)->exists() || ProductPreview::where('code', $code)->exists());

            ProductPreview::firstOrCreate([
                'name' => $row['name'],
                'category_id' => $category_id ? $category_id->id : null,
            ], [
                'status' => $row['status'],
                'is_favorite' => strtolower($row['favorite']) == 'yes' ? true : false,
                'code' => $code,
                'imei' => $imei,
                'desc' => $row['description'],
                'error' => $error,
            ]);
        }
    }
}
