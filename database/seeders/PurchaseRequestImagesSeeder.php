<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PurchaseRequestImagesSeeder extends Seeder
{
    public function run(): void
    {
        $images = [
            [
                'purchase_request_id' => 1,
                'file_name' => 'request1_photo1.jpg',
                'file_path' => 'storage/purchase_requests/request1_photo1.jpg',
                'file_type' => 'image/jpeg',
                'file_size' => 150000,
                'uploaded_by' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'purchase_request_id' => 1,
                'file_name' => 'request1_photo2.jpg',
                'file_path' => 'storage/purchase_requests/request1_photo2.jpg',
                'file_type' => 'image/jpeg',
                'file_size' => 175000,
                'uploaded_by' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'purchase_request_id' => 2,
                'file_name' => 'request2_photo1.jpg',
                'file_path' => 'storage/purchase_requests/request2_photo1.jpg',
                'file_type' => 'image/jpeg',
                'file_size' => 200000,
                'uploaded_by' => 4,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('purchase_request_images')->insert($images);
    }
}