<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UnitSeeder extends Seeder
{
    public function run(): void
    {
        $units = [

            // الوزن
            ['name' => 'كيلوغرام', 'description' => 'وحدة قياس الوزن', 'code' => 'kg'],
            ['name' => 'غرام', 'description' => 'وحدة قياس الوزن', 'code' => 'g'],
            ['name' => 'طن', 'description' => 'وحدة قياس الوزن', 'code' => 'ton'],

            // الطول
            ['name' => 'متر', 'description' => 'وحدة قياس الطول', 'code' => 'm'],
            ['name' => 'سنتيمتر', 'description' => 'وحدة قياس الطول', 'code' => 'cm'],
            ['name' => 'مليمتر', 'description' => 'وحدة قياس الطول', 'code' => 'mm'],
            ['name' => 'كيلومتر', 'description' => 'وحدة قياس الطول', 'code' => 'km'],

            // الحجم
            ['name' => 'لتر', 'description' => 'وحدة قياس الحجم', 'code' => 'l'],
            ['name' => 'مليلتر', 'description' => 'وحدة قياس الحجم', 'code' => 'ml'],

            // العد
            ['name' => 'قطعة', 'description' => 'وحدة عد', 'code' => 'pcs'],
            ['name' => 'علبة', 'description' => 'وحدة تعبئة', 'code' => 'box'],
            ['name' => 'كرتون', 'description' => 'وحدة تعبئة', 'code' => 'carton'],
            ['name' => 'حبة', 'description' => 'وحدة عد', 'code' => 'item'],
            ['name' => 'طقم', 'description' => 'مجموعة قطع', 'code' => 'set'],

        ];

        foreach ($units as $unit) {
            DB::table('units')->insert([
                'name' => $unit['name'],
                'description' => $unit['description'],
                'code' => $unit['code'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}