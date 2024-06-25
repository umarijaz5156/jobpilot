<?php

namespace Database\Seeders;

use App\Models\MenuSettings;
use Illuminate\Database\Seeder;

class MenuSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $menus = [
            [
                'en_title' => 'Home',
                'bn_title' => 'হোম',
                'hi_title' => 'घर',
                'fr_title' => 'Maison',
                'es_title' => 'Hogar',
                'id_title' => 'Rumah',
                'de_title' => 'Heim',
                'ar_title' => 'بيت',
                'url' => '/',
                'status' => true,
                'order' => 0,
                'default' => true,
                'for' => ['public', 'employee', 'candidate'],
            ],
            [
                'en_title' => 'Find Job',
                'bn_title' => 'চাকরী খোজা',
                'hi_title' => 'नौकरी ढूंढो',
                'fr_title' => 'Trouver Un Emploi',
                'es_title' => 'Encuentra Trabajo',
                'id_title' => 'Mencari Pekerjaan',
                'de_title' => 'Job Finden',
                'ar_title' => 'ابحث عن وظيفة',
                'url' => '/jobs',
                'status' => true,
                'order' => 1,
                'default' => true,
                'for' => ['public', 'candidate'],
            ],
            [
                'en_title' => 'Candidates',
                'bn_title' => 'প্রার্থী',
                'hi_title' => 'उम्मीदवार',
                'fr_title' => 'Candidates',
                'es_title' => 'Candidates',
                'id_title' => 'Kandidat',
                'de_title' => 'Kandidatinnen',
                'ar_title' => 'مرشحين',
                'url' => '/candidates',
                'status' => true,
                'order' => 2,
                'default' => true,
                'for' => ['public', 'employee'],
            ],
            [
                'en_title' => 'Companies',
                'bn_title' => 'কোম্পানিগুলো',
                'hi_title' => 'कंपनियों',
                'fr_title' => 'Entreprises',
                'es_title' => 'Compañías',
                'id_title' => 'Perusahaan',
                'de_title' => 'Firmen',
                'ar_title' => 'شركات',
                'url' => '/employers',
                'status' => true,
                'order' => 3,
                'default' => true,
                'for' => ['public', 'candidate'],
            ],
            [
                'en_title' => 'Blog',
                'bn_title' => 'ব্লগ',
                'hi_title' => 'ब्लॉग',
                'fr_title' => 'Blog',
                'es_title' => 'Blog',
                'id_title' => 'Blog',
                'de_title' => 'Blog',
                'ar_title' => 'مدونة',
                'url' => '/posts',
                'status' => true,
                'order' => 4,
                'default' => true,
                'for' => ['public'],
            ],
            [
                'en_title' => 'Pricing',
                'bn_title' => 'মূল্য নির্ধারণ',
                'hi_title' => 'मूल्य निर्धारण',
                'fr_title' => 'Tarifs',
                'es_title' => 'Precios',
                'id_title' => 'Harga',
                'de_title' => 'Preisgestaltung',
                'ar_title' => 'التسعير',
                'url' => '/plans',
                'status' => true,
                'order' => 5,
                'default' => true,
                'for' => ['public', 'employee'],
            ],
            [
                'en_title' => 'Dashboard',
                'bn_title' => 'ড্যাশবোর্ড',
                'hi_title' => 'डैशबोर्ड',
                'fr_title' => 'Tableau De Bord',
                'es_title' => 'Panel',
                'id_title' => 'Dasbor',
                'de_title' => 'Armaturenbrett',
                'ar_title' => 'لوحة القيادة',
                'url' => '/company/dashboard',
                'status' => true,
                'order' => 6,
                'default' => true,
                'for' => ['employee'],
            ],
            [
                'en_title' => 'My Job',
                'bn_title' => 'আমার কাজ',
                'hi_title' => 'मेरी नौकरी',
                'fr_title' => 'Mon Boulot',
                'es_title' => 'Mi Trabajo',
                'id_title' => 'Pekerjaan saya',
                'de_title' => 'Mein Job',
                'ar_title' => 'عملي',
                'url' => '/company/my-jobs',
                'status' => true,
                'order' => 7,
                'default' => true,
                'for' => ['employee'],
            ],
            [
                'en_title' => 'Dashboard',
                'bn_title' => 'ড্যাশবোর্ড',
                'hi_title' => 'डैशबोर्ड',
                'fr_title' => 'Tableau De Bord',
                'es_title' => 'Panel',
                'id_title' => 'Dasbor',
                'de_title' => 'Armaturenbrett',
                'ar_title' => 'لوحة القيادة',
                'url' => '/candidate/dashboard',
                'status' => true,
                'order' => 8,
                'default' => true,
                'for' => ['candidate'],
            ],
            [
                'en_title' => 'Job Alert',
                'bn_title' => 'চাকরির সতর্কীকরণ',
                'hi_title' => 'जॉब अलर्ट',
                'fr_title' => "Alerte D'emploi",
                'es_title' => 'Alerta De Trabajo',
                'id_title' => 'Pemberitahuan Pekerjaan',
                'de_title' => 'Jobalarm',
                'ar_title' => 'حالة تأهب وظيفة',
                'url' => '/candidate/job/alerts',
                'status' => true,
                'order' => 9,
                'default' => true,
                'for' => ['candidate'],
            ],
        ];

        foreach ($menus as $key => $item) {

            $menu = new MenuSettings();
            $menu->url = $item['url'];
            $menu->status = $item['status'];
            $menu->default = $item['default'];
            $menu->order = $item['order'];
            $menu->for = json_encode($item['for']);
            $menu->save();

            $menu->translateOrNew('en')->title = $item['en_title'];
            $menu->translateOrNew('bn')->title = $item['bn_title'];
            $menu->translateOrNew('hi')->title = $item['hi_title'];
            $menu->translateOrNew('fr')->title = $item['fr_title'];
            $menu->translateOrNew('es')->title = $item['es_title'];
            $menu->translateOrNew('id')->title = $item['id_title'];
            $menu->translateOrNew('de')->title = $item['de_title'];
            $menu->translateOrNew('ar')->title = $item['ar_title'];
            $menu->save();
        }
    }
}
