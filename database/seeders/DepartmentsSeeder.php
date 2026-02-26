<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DepartmentsSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        $departments = [
            ['id' => 7,  'name' => 'الشؤون المالية',                     'code' => 'mali',                               'manager_user_id' => null],
            ['id' => 8,  'name' => 'التطوير والتنمية المستدامة',        'code' => 'DEVELOP',                            'manager_user_id' => 1],
            ['id' => 9,  'name' => 'قسم المخازن',                       'code' => '1100',                               'manager_user_id' => null],
            ['id' => 11, 'name' => 'التدقيق والرقابة الداخلية',          'code' => 'DETECT',                             'manager_user_id' => null],
            ['id' => 12, 'name' => 'العلاقات العامة',                     'code' => 'ALAALAKAT-ALAAAM',                  'manager_user_id' => null],
            ['id' => 13, 'name' => 'المضيف',                             'code' => 'ALMDYF',                             'manager_user_id' => null],
            ['id' => 14, 'name' => 'الاليات',                             'code' => 'ALALYAT',                            'manager_user_id' => null],
            ['id' => 15, 'name' => 'الاعلام',                             'code' => 'ALAAALAM',                            'manager_user_id' => null],
            ['id' => 16, 'name' => 'حفظ النظام',                          'code' => 'HFTH-ALNTHAM',                       'manager_user_id' => null],
            ['id' => 17, 'name' => 'بين الحرمين الشريفين',               'code' => 'BYN-ALHRMYN-ALSHRYFYN',             'manager_user_id' => null],
            ['id' => 18, 'name' => 'المواقع الخارجية',                    'code' => 'ALMOAKAA-ALKHARGY',                 'manager_user_id' => null],
            ['id' => 19, 'name' => 'الشؤون الفكرية',                     'code' => 'ALSHOON-ALFKRY',                     'manager_user_id' => null],
            ['id' => 20, 'name' => 'حفظ النظام',                          'code' => 'HFTH-ALNTHAM1',                      'manager_user_id' => null],
            ['id' => 21, 'name' => 'الشؤون الخدمية',                     'code' => 'ALSHOON-ALKHDMY',                    'manager_user_id' => null],
            ['id' => 22, 'name' => 'الشؤون القانونية',                    'code' => 'ALSHOON-ALKANONY',                   'manager_user_id' => null],
            ['id' => 23, 'name' => 'الاتصالات والامن المعلوماتي',        'code' => 'ALATSALAT-OALAMN-ALMAALOMATY',      'manager_user_id' => null],
            ['id' => 24, 'name' => 'شؤون المعارف',                        'code' => 'SHOON-ALMAAARF',                     'manager_user_id' => null],
            ['id' => 25, 'name' => 'الصيانة والانشائات الهندسية',        'code' => 'ALSYAN-OALANSHAYAT-ALHNDSY',        'manager_user_id' => null],
            ['id' => 26, 'name' => 'الشؤون الادارية',                     'code' => 'ALSHOON-ALADARY',                    'manager_user_id' => null],
            ['id' => 27, 'name' => 'مجلس الادارة',                        'code' => 'MGLS-ALADAR',                        'manager_user_id' => null],
            ['id' => 28, 'name' => 'المشاريع الهندسية',                    'code' => 'ALMSHARYAA-ALHNDSY',                 'manager_user_id' => null],
            ['id' => 29, 'name' => 'شعبة الحرم الشريف',                   'code' => 'SHAAB-ALHRM-ALSHRYF',               'manager_user_id' => null],
            ['id' => 30, 'name' => 'شركة الكفيل للصناعات الغذائة',       'code' => 'SALA',                               'manager_user_id' => null],
            ['id' => 31, 'name' => 'المجمع العملي للقران الكريم',          'code' => 'AALA',                               'manager_user_id' => null],
            ['id' => 32, 'name' => 'الشعائر والمواكب والهيئات الحسينية',   'code' => 'AOOA',                               'manager_user_id' => null],
            ['id' => 33, 'name' => 'مجمع العباس السكني',                   'code' => 'MGMAA-ALAABAS-ALSKNY',              'manager_user_id' => null],
            ['id' => 34, 'name' => 'استلام الهدايا والنذور',              'code' => 'ASTLAM-ALHDAYA-OALNTHOR',           'manager_user_id' => null],
            ['id' => 35, 'name' => 'شعبة السادة الخدم',                   'code' => 'SHAAB-ALSAD-ALKHDM',                'manager_user_id' => null],
            ['id' => 36, 'name' => 'المتحف للنفائس والمخطوطات',           'code' => 'ALMTHF-LLNFAYS-OALMKHTOTAT',       'manager_user_id' => null],
            ['id' => 37, 'name' => 'رعاية الصحن الشريف',                  'code' => 'RAAAY-ALSHN-ALSHRYF',               'manager_user_id' => null],
            ['id' => 38, 'name' => 'مستشفى الكفيل التخصصي',               'code' => 'MSTSHF-ALKFYL-ALTKHSSY',            'manager_user_id' => null],
            ['id' => 39, 'name' => 'مقام الامام المهدي',                   'code' => 'MKAM-ALAMAM-ALMHDY',                'manager_user_id' => null],
            ['id' => 40, 'name' => 'فرقة العباس القتالية',                 'code' => 'FRK-ALAABAS-ALKTALY',               'manager_user_id' => null],
            ['id' => 41, 'name' => 'الشؤون الدينية',                      'code' => 'ALSHOON-ALDYNY',                     'manager_user_id' => null],
            ['id' => 42, 'name' => 'السياحة الدينية',                      'code' => 'ALSYAH-ALDYNY',                      'manager_user_id' => null],
            ['id' => 43, 'name' => 'شركة نور الكفيل',                       'code' => 'SHRK-NOR-ALKFYL',                    'manager_user_id' => null],
            ['id' => 44, 'name' => 'مجمع العلقمي',                         'code' => 'MGMAA-ALAALKMY',                     'manager_user_id' => null],
            ['id' => 45, 'name' => 'مركز الكفيل',                          'code' => 'MRKZ-ALKFYL',                        'manager_user_id' => null],
            ['id' => 46, 'name' => 'مركز الكفيل',                          'code' => 'MRKZ-ALKFYL1',                       'manager_user_id' => null],
            ['id' => 47, 'name' => 'التربية والتعليم',                     'code' => 'ALTRBY-OALTAALYM',                  'manager_user_id' => null],
            ['id' => 48, 'name' => 'مؤسسة الوافي',                         'code' => 'MOSS-ALOAFY',                        'manager_user_id' => null],
            ['id' => 49, 'name' => 'المولدات',                             'code' => 'ALMOLDAT',                            'manager_user_id' => null],
            ['id' => 50, 'name' => 'مجمع الامام الصادق',                   'code' => 'MGMAA-ALAMAM-ALSADK',               'manager_user_id' => null],
            ['id' => 51, 'name' => 'موقع الصناعات والحرف الفنية',          'code' => 'MAOA',                               'manager_user_id' => null],
            ['id' => 52, 'name' => 'شعبة الزينبيات',                       'code' => 'SHAAB-ALZYNBYAT',                   'manager_user_id' => null],
            ['id' => 53, 'name' => 'الحزام الاخضر الاول',                   'code' => 'ALHZAM-ALAKHDR-ALAOL',              'manager_user_id' => null],
        ];

        foreach ($departments as $dept) {
            DB::table('departments')->updateOrInsert(
                ['id' => $dept['id']],
                array_merge($dept, ['created_at' => $now, 'updated_at' => $now])
            );
        }
    }
}