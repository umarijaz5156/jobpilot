<?php

use Modules\Language\Entities\Language;

beforeEach(function () {
    // Create an admin user using the 'createAdmin' function.
    $this->admin = createAdmin();
    actingAs($this->admin, 'admin');
});

// This test verifies that an admin can access the language index page.
it('test admin can access language page', function () {
    // Send a GET request to the 'admin/settings/languages' route and assert the response status.
    $this->get(route('languages.index'))
        ->assertStatus(200) // Expecting a 200 (OK) response status.
        ->assertViewIs('language::index'); // Assert that the view being returned is 'language::index'.
});

// This test verifies that an admin can access the language create page.
it('test admin can access language create page', function () {
    // Send a GET request to the 'admin/settings/languages' route and assert the response status.
    $this->get(route('languages.create'))
        ->assertStatus(200) // Expecting a 200 (OK) response status.
        ->assertViewIs('language::create') // Assert that the view being returned is 'language::index'.
        ->assertViewHas('translations'); // Assert that the view being returned is 'data language'.
});
