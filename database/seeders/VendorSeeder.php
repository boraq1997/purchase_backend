<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class VendorSeeder extends Seeder
{
    public function run(): void
    {
        $vendors = [
            [
                'name' => 'شركة ألفا للمعدات',
                'phone1' => '07710000001',
                'phone2' => '07710000002',
                'email' => 'alpha@example.com',
                'address' => 'شارع الصناعة، بغداد',
                'created_by' => 1, // superadmin id
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'شركة بيتا للمواد',
                'phone1' => '07710000003',
                'phone2' => null,
                'email' => 'beta@example.com',
                'address' => 'منطقة التجارة، بغداد',
                'created_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'شركة جاما للخدمات',
                'phone1' => '07710000004',
                'phone2' => '07710000005',
                'email' => 'gamma@example.com',
                'address' => 'شارع بغداد الدولي',
                'created_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'شركة دلتا للمشتريات',
                'phone1' => '07710000006',
                'phone2' => null,
                'email' => 'delta@example.com',
                'address' => 'منطقة الكرخ، بغداد',
                'created_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('vendors')->insert($vendors);
    }
}