<?php

use App\Models\ManualPayment;

beforeEach(function () {
    // Create an admin user using the 'createAdmin' function.
    $this->admin = createAdmin();
    actingAs($this->admin, 'admin');
});

// Test Case: Admin can visit the manual payment index page
it('admin can visit manual payment index page', function () {
    $this->get(route('settings.payment.manual'))
        ->assertStatus(200) // Ensure a successful response status code
        ->assertViewIs('backend.settings.pages.payment-manual'); // Ensure the correct view is returned
});

// Test Case: Admin can store manual payment
it('admin can store manual payment', function () {
    $this->post(route('settings.payment.manual.store'), [
        'name' => 'Airtel',
        'type' => 'check_payment',
        'description' => 'this is description',
    ])->assertStatus(302); // Ensure a successful redirect after storing payment
    expect(ManualPayment::count())->toBe(1); // Check if the payment was successfully stored in the database
});

// Test Case: Admin fails to store manual payment due to validation errors
it('admin fails to store manual payment for validation', function () {
    $this->post(route('settings.payment.manual.store'), [])
        ->assertSessionHasErrors(['name', 'type', 'description']) // Check if validation errors are present in the session
        ->assertStatus(302); // Ensure a redirect due to validation failure
    expect(ManualPayment::count())->toBe(0); // Verify that no payment was stored in the database
});

// Test Case: Admin can visit manual payment edit page
it('admin can visit manual payment edit page', function () {
    ManualPayment::create([
        'name' => 'Airtel',
        'type' => 'check_payment',
        'description' => 'this is description',
    ]);
    $this->get(route('settings.payment.manual.edit', 1))
        ->assertStatus(200) // Ensure a successful response status code
        ->assertViewIs('backend.settings.pages.payment-manual'); // Ensure the correct view is returned
});

// Test Case: Admin can update manual payment
it('admin can update manual payment', function () {
    ManualPayment::create([
        'name' => 'Airtel',
        'type' => 'check_payment',
        'description' => 'this is description',
    ]);
    $this->put(route('settings.payment.manual.update', 1), [
        'name' => 'Bangladesh',
        'type' => 'check_payment',
        'description' => 'this is description',
    ])->assertStatus(302); // Ensure a successful redirect after updating payment
    expect(ManualPayment::first()->name)->toBe('Bangladesh'); // Check if the payment name was successfully updated
});

// Test Case: Admin fails to update manual payment due to validation errors
it('admin fails to update manual payment for validation check', function () {
    ManualPayment::create([
        'name' => 'Airtel',
        'type' => 'check_payment',
        'description' => 'this is description',
    ]);
    $this->put(route('settings.payment.manual.update', 1), [])
        ->assertSessionHasErrors(['name', 'type', 'description']) // Check if validation errors are present in the session
        ->assertStatus(302); // Ensure a redirect due to validation failure
    expect(ManualPayment::first()->name)->toBe('Airtel'); // Verify that the payment name remains unchanged
});

// Test Case: Admin can delete a manual payment
it('admin can delete manual payment', function () {
    ManualPayment::create([
        'name' => 'Airtel',
        'type' => 'check_payment',
        'description' => 'this is description',
    ]);
    $this->delete(route('settings.payment.manual.delete', 1)); // Attempt to delete a manual payment
    expect(ManualPayment::count())->toBe(0); // Verify that the payment was successfully deleted from the database
});

// Test Case: Admin can change manual payment status
it('admin can change manual payment status', function () {
    $data = [
        'name' => 'Airtel',
        'type' => 'check_payment',
        'description' => 'this is description',
    ];
    ManualPayment::create($data);
    $this->get(route('settings.payment.manual.status'), [
        'id' => 1,
        'status' => 1,
    ]);
    expect(ManualPayment::first()->status)->toBe(1); // Check if the payment status was successfully updated
});
