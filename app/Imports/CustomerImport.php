<?php

namespace App\Imports;

use App\Models\CustomerPreview;
use App\Models\Group;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class CustomerImport implements ToCollection, WithHeadingRow
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
            if($row['phone'] == null) {
                $error = 'phone is required';
            }
            if($row['address'] == null) {
                $error = 'address is required';
            }
            if($row['group_id'] == null) {
                $error = 'group is required';
            } else {
                $group = Group::where('id', $row['group_id'])->first();
                if ($group) {
                    $group_id = $group->id;
                } else {
                    $error = 'group is error. Check Group Name';
                }
            }
            CustomerPreview::firstOrCreate([
                'name' => $row['name'],
                'phone' => $row['phone'],
                'email' => $row['email'],
                'address' => $row['address'],
                'group_id' => $group_id,
                'error' => $error,
            ]);
        }
    }
}
