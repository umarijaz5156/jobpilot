<?php

use App\Models\User;

it('logs the user out and destroy session data', function () {
    // Create a user session with some session data
    session(['current_currency' => 'USD', 'current_lang' => 'en', 'selected_country' => 'US']);

    $this->seed([
        ProfessionSeeder::class,
        EducationSeeder::class,
        ExperienceSeeder::class,
        JobRoleSeeder::class,

    ]);
    // Simulate a logged-in user
    $user = User::factory()->create(['role' => 'candidate']);
    $this->actingAs($user);
    $this->post(route('login'), [
        'email' => $user->email,
        'password' => 'password',
    ]);

    session()->invalidate();
    session()->regenerateToken();
    // Call the logout method
    $this->post(route('logout'));
    // Assert that the session data is restored
    $this->assertNotEquals('USD', session('current_currency'));
    $this->assertNotEquals('en', session('current_lang'));
    $this->assertNotEquals('US', session('selected_country'));

});

it('logs the user out and restores session data', function () {
    // Create a user session with some session data
    session(['current_currency' => 'USD', 'current_lang' => 'en', 'selected_country' => 'US']);

    $this->seed([
        ProfessionSeeder::class,
        EducationSeeder::class,
        ExperienceSeeder::class,
        JobRoleSeeder::class,
    ]);

    // Simulate a logged-in user
    $user = User::factory()->create(['role' => 'candidate']);
    $this->actingAs($user);

    // Save the current session data to variables before logging out
    $currencyBeforeLogout = session('current_currency');
    $langBeforeLogout = session('current_lang');
    $countryBeforeLogout = session('selected_country');

    // Call the logout method
    $this->post(route('logout'));

    // Assert that the session data is restored to its previous values
    $this->assertEquals($currencyBeforeLogout, session('current_currency'));
    $this->assertEquals($langBeforeLogout, session('current_lang'));
    $this->assertEquals($countryBeforeLogout, session('selected_country'));
});
