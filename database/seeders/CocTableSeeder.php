<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CocTable; // Import your model

class CocTableSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            // CV types from your screenshot
            ['coc_no' => '17365401', 'coc_type' => 'CV', 'coc_status' => 'Used'],
            ['coc_no' => '17365402', 'coc_type' => 'CV', 'coc_status' => 'Used'],
            ['coc_no' => '17365403', 'coc_type' => 'CV', 'coc_status' => 'Available'],
            ['coc_no' => '17365404', 'coc_type' => 'CV', 'coc_status' => 'Available'],
            
            // MC types from your screenshot
            ['coc_no' => '16739001', 'coc_type' => 'MC', 'coc_status' => 'Used'],
            ['coc_no' => '16739002', 'coc_type' => 'MC', 'coc_status' => 'Used'],
            ['coc_no' => '16739003', 'coc_type' => 'MC', 'coc_status' => 'Used'],
            ['coc_no' => '16739004', 'coc_type' => 'MC', 'coc_status' => 'Used'],
            ['coc_no' => '16739005', 'coc_type' => 'MC', 'coc_status' => 'Available'],

            ['coc_no' => '17809001', 'coc_type' => 'PC', 'coc_status' => 'Used'],
            ['coc_no' => '17809002', 'coc_type' => 'PC', 'coc_status' => 'Available'],
            ['coc_no' => '17809003', 'coc_type' => 'PC', 'coc_status' => 'Available'],

            ['coc_no' => '14143011', 'coc_type' => 'TC', 'coc_status' => 'Available'],
            ['coc_no' => '14143012', 'coc_type' => 'TC', 'coc_status' => 'Used'],
            ['coc_no' => '14143013', 'coc_type' => 'TC', 'coc_status' => 'Available'],
        ];

        foreach ($data as $item) {
            CocTable::create($item); // Insert each record
        }
    }
}