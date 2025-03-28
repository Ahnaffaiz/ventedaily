<?php

namespace App\Imports;

use App\Models\CategoryPreview;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class CategoryImport implements ToCollection, WithHeadingRow
{
    /**
    * @param Collection $collection
    */
    public function collection(Collection $rows)
    {
        foreach ($rows as $key => $row) {
            $error = null;
            if($row['name'] == null) {
                $error = 'name is required';
            }
            CategoryPreview::firstOrCreate([
                'name' => $row['name'],
                'desc' => $row['description'],
                'error' => $error,
            ]);
        }
    }
}
