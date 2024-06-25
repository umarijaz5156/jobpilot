<?php

use Modules\Location\Entities\Country;

beforeEach(function () {
    $this->admin = createAdmin();

    actingAs($this->admin, 'admin');
});

it('admin can visit country index page', function () {
    $this->get(route('module.country.index'))->assertStatus(200);
});

it('admin can visit country create page', function () {
    $this->get(route('module.country.create'))->assertStatus(200);
});

test('admin create country validation errors and redirect back to create page', function () {
    $this->post(route('module.country.store', [
        'name' => '',
        'image' => '',
        'icon' => '',
    ]))
        ->assertStatus(302)
        ->assertSessionHasErrors(['name', 'image', 'icon'])
        ->assertInvalid(['name', 'image', 'icon']);
});

test('admin create country unique validation errors and redirect back to create page', function () {
    Country::factory()->create(['name' => 'Bangladesh']);

    $this->post(route('module.country.store', [
        'name' => 'Bangladesh',
        'image' => fake()->imageUrl(),
    ]))
        ->assertStatus(302)
        ->assertSessionHasErrors(['name'])
        ->assertInvalid(['name']);
});

it('admin can visit country edit page', function () {
    $country = Country::factory()->create();

    $this->get(route('module.country.edit', $country->id))->assertStatus(200);
});

test('admin update country validation errors and redirect back to create page', function () {
    $country = Country::factory()->create();

    $this->put(route('module.country.update', $country->id), [
        'name' => '',
    ])
        ->assertStatus(302)
        ->assertSessionHasErrors(['name'])
        ->assertInvalid(['name']);
});

test('admin update country unique validation errors and redirect back to create page', function () {
    $country = Country::factory()->create(['name' => 'Bangladesh']);

    $this->put(route('module.country.update', $country->id), [
        'name' => 'Bangladesh',
        'image' => fake()->imageUrl(),
    ])
        ->assertStatus(302);
});

test('admin can delete country', function () {
    $country = Country::factory()->create();

    $this->get(route('module.country.delete', $country->id))->assertStatus(200);
});

test('admin can delete multiple countries', function () {
    $countries = Country::factory(10)->create();

    $this->get(route('module.country.delete', $countries->pluck('id')->toArray()))->assertStatus(200);
});
