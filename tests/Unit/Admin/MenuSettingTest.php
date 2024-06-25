<?php

namespace Tests\Unit\Admin;

use App\Models\MenuSettings;
use App\Models\MenuSettingsTranslation;
use Database\Seeders\MenuSettingsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MenuSettingTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function test_the_default_menu_list__is_exist_on_database()
    {
        $this->seed(MenuSettingsSeeder::class);

        $modelCount = MenuSettings::count();
        $expectedMinCount = 5;
        $this->assertGreaterThan($expectedMinCount, $modelCount);
    }

    public function test_the_default_menu_translation__is_exist_on_database()
    {
        $this->seed(MenuSettingsSeeder::class);

        $modelCount = MenuSettingsTranslation::count();
        $expectedMinCount = 5;
        $this->assertGreaterThan($expectedMinCount, $modelCount);
    }

    // public function test_the_menu_list_page_returns_a_successful_response()
    // {
    //     // $credentials = [
    //     //     'email' => 'admin-email@example.com',
    //     //     'password' => 'admin-password',
    //     // ];

    //     // $this->seed(SettingSeeder::class);
    //     // $this->seed(WebsiteSettingSeeder::class);

    //     // $this->seed(LanguageDatabaseSeeder::class);
    //     // $lang = Language::first();
    //     // session()->put('current_lang', $lang);

    //     // // Assuming 'Admin' is the model representing the admin user.
    //     // $role = Role::first();
    //     // $admin = new Admin();
    //     // $admin->name = "Admin";
    //     // $admin->email = "admin@mail.com";
    //     // $admin->image = "backend/image/default.png";
    //     // $admin->password = bcrypt('password');
    //     // $admin->email_verified_at = Carbon::now();
    //     // $admin->remember_token = Str::random(10);
    //     // $admin->save();
    //     // $admin->assignRole($role);

    //     // // Authenticating the user
    //     // $this->actingAs($admin);

    //     // $this->seed(MenuSettingsSeeder::class);

    //     // $response = $this->actingAs($admin, 'admin')->get('/admin/settings/menu-settings/1/edit');
    //     // $response->assertStatus(200);
    // }
}
