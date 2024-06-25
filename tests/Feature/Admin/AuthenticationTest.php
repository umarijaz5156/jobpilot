<?php

use Database\Seeders\AdminSeeder;

it('gives back a successful response for the admin login page', function () {
    $this->get(route('admin.login'))->assertStatus(200);
});

test('admin login validation and redirect back to form', function () {
    $this->post(route('admin.login'), [
        'email' => '',
        'password' => '',
    ])
        ->assertStatus(302)
        ->assertSessionHasErrors(['email', 'password'])
        ->assertInvalid(['email', 'password']);
});

test('admin can login their account', function () {

    config(['captcha.active' => false]);

    // Seed newly admin account for login
    $this->seed(AdminSeeder::class);

    // Login with newly admin account
    $this->post(route('admin.login'), [
        'email' => 'admin@mail.com',
        'password' => 'password',
    ])
        ->assertStatus(302)
        ->assertRedirect('admin/dashboard');
});

it('gives back a successful response for the forget password page', function () {
    $this->get(route('admin.password.email'))->assertStatus(200);
});

it('gives back a successful response for the reset password page', function () {
    // Seed newly admin account
    $this->seed(AdminSeeder::class);

    // Forget password request sent
    $token = uniqid();
    $email = 'admin@mail.com';

    $this->post(route('admin.password.email', [
        'token' => $token,
        'email' => $email,
    ]))->assertStatus(302);

    // Reset password
    $this->post(route('admin.password.update', [
        'token' => $token,
        'email' => $email,
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]))->assertStatus(302);
});
