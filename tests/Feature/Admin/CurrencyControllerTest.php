<?php

use Modules\Currency\Entities\Currency;

beforeEach(function () {
    // Create an admin user using the 'createAdmin' function.
    $this->admin = createAdmin();

    // Act as the authenticated admin user for the testing session.
    actingAs($this->admin, 'admin');
    $this->seed([SettingSeeder::class]);
});

// Test case to check if the admin can visit the currency index page.
it('admin can visit currency index page', function () {
    $this->get(route('module.currency.index'))
        ->assertstatus(200)
        ->assertViewIs('currency::index')
        ->assertViewHas('currencies');
});

// Test case to check if the admin can visit the currency create page.
it('admin can visit currency create page', function () {
    $this->get(route('module.currency.create'))
        ->assertstatus(200)
        ->assertViewIs('currency::create');
});

// Test case to check if the admin can store a currency.
it('admin can store a currency', function () {
    $data = [
        'name' => 'Airtel',
        'code' => 'hdd',
        'symbol' => '$',
        'symbol_position' => 'left',
    ];
    $this->post(route('module.currency.store'), $data)
        ->assertstatus(302);
});

// Test case to check if the admin fails to store a currency due to validation errors.
it('admin fails to store a currency for validation', function () {
    $data = [
        //
    ];
    $this->post(route('module.currency.store'), $data)
        ->assertSessionHasErrors(['name', 'code', 'symbol'])
        ->assertstatus(302);
});

// Test case to check if the admin can visit the currency edit page.
it('admin can visit currency edit page', function () {
    $this->get(route('module.currency.edit', 1))
        ->assertstatus(200)
        ->assertViewIs('currency::edit');
});

// Test case to check if the admin fails to update a currency due to validation errors .
it('admin fails to update a currency for validation', function () {
    $data = [
        'name' => 'Airtel',
        'code' => 'hdd',
        'symbol' => '$',
        'symbol_position' => 'left',
    ];
    $this->post(route('module.currency.store'), $data);
    $data = [
        //
    ];
    $this->put(route('module.currency.update', 1), $data)
        ->assertSessionHasErrors(['name', 'code', 'symbol'])
        ->assertstatus(302);
});
