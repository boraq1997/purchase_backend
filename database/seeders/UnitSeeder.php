<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UnitSeeder extends Seeder
{
    public function run(): void
    {
        $units = [
            ['name' => 'قطعة', 'code' => 'pcs', 'description' => 'وحدة عد للقطع'],
            ['name' => 'كيلوغرام', 'code' => 'kg', 'description' => 'وحدة قياس الوزن'],
            ['name' => 'غرام', 'code' => 'g', 'description' => 'وحدة قياس الوزن الصغيرة'],
            ['name' => 'طن', 'code' => 'ton', 'description' => 'وحدة قياس الوزن الكبيرة'],
            ['name' => 'متر', 'code' => 'm', 'description' => 'وحدة قياس الطول'],
            ['name' => 'سنتيمتر', 'code' => 'cm', 'description' => 'وحدة قياس الطول الصغيرة'],
            ['name' => 'كيلومتر', 'code' => 'km', 'description' => 'وحدة قياس المسافة'],
            ['name' => 'لتر', 'code' => 'l', 'description' => 'وحدة قياس السوائل'],
            ['name' => 'مليلتر', 'code' => 'ml', 'description' => 'وحدة قياس السوائل الصغيرة'],
            ['name' => 'علبة', 'code' => 'box', 'description' => 'وحدة تعبئة'],
            ['name' => 'كرتون', 'code' => 'carton', 'description' => 'وحدة تعبئة كبيرة'],
            ['name' => 'طقم', 'code' => 'set', 'description' => 'مجموعة قطع'],
            ['name' => 'رول', 'code' => 'roll', 'description' => 'وحدة لفائف'],
            ['name' => 'كيس', 'code' => 'bag', 'description' => 'وحدة أكياس'],
        ];

        DB::table('units')->insert($units);
    }
}