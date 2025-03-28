<?php

namespace App\Imports;

use App\Models\SizePreview;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class SizeImport implements ToCollection, WithHeadingRow
{
    /**
    * @param Collection $collection
    */
    public function collection(Collection $rows)
    {

        foreach ($rows as $row) {
            if (!array_filter($row->toArray())) {
                return null;
            }
            $error = null;
            if($row['name'] == null) {
                $error = 'name is required';
            }
            SizePreview::firstOrCreate([
                'name' => $row['name'],
                'desc' => $row['description'],
                'error' => $error,
            ]);
        }
    }
}
