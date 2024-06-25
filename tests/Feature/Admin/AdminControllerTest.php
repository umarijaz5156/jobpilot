<?php

use Database\Seeders\CompanySeeder;
use Database\Seeders\EarningSeeder;
use Database\Seeders\ExperienceSeeder;
use Database\Seeders\IndustryTypeSeeder;
use Database\Seeders\JobRoleSeeder;
use Database\Seeders\ManualPaymentSeeder;
use Database\Seeders\OrganizationTypeSeeder;
use Database\Seeders\ProfessionSeeder;

beforeEach(function () {
    $this->admin = createAdmin();
    $this->seed([
        ManualPaymentSeeder::class,
        IndustryTypeSeeder::class,
        OrganizationTypeSeeder::class,
        JobRoleSeeder::class,
        ProfessionSeeder::class,
        ExperienceSeeder::class,
        CompanySeeder::class,
        EarningSeeder::class,
    ]);
    actingAs($this->admin, 'admin');
});

test('user can visit ad listing page', function () {
    $response = $this->get(route('settings.ad_setting'));
    $response->assertStatus(200);
});
// it('admin can download transaction invoice page', function () {
//     $response = $this->post(route('admin.transaction.invoice.download',1));

//     $response->assertStatus(200);
// });
