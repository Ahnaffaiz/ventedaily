<?php

namespace App\Imports;

use App\Models\ColorPreview;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ColorImport implements ToCollection, WithHeadingRow
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
            $error = null;
            if($row['name'] == null) {
                $error = 'name is required';
            }
            ColorPreview::firstOrCreate([
                'name' => $row['name'],
                'desc' => $row['description'],
                'error' => $error,
            ]);
        }
    }
}
