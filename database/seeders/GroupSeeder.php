<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('groups')->insert([
            [
                'name' => 'Reseller',
                'desc' => 'Reseller Group',
            ],
            [
                'name' => 'Online',
                'desc' => 'Online Group',
            ]
        ]);
    }
}
