<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EstimateImagesSeeder extends Seeder
{
    public function run(): void
    {
        $images = [
            [
                'estimate_id' => 1,
                'file_name' => 'estimate1_photo1.jpg',
                'file_path' => 'storage/estimates/estimate1_photo1.jpg',
                'file_type' => 'image/jpeg',
                'file_size' => 120000,
                'uploaded_by' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'estimate_id' => 2,
                'file_name' => 'estimate2_photo1.jpg',
                'file_path' => 'storage/estimates/estimate2_photo1.jpg',
                'file_type' => 'image/jpeg',
                'file_size' => 145000,
                'uploaded_by' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('estimate_images')->insert($images);
    }
}