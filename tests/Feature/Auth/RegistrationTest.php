<?php

use App\Models\User;

test('registration screen can be rendered', function () {
    $response = $this->get('/register');

    $response->assertStatus(200);
});

test('new users can register', function () {
    $response = $this->post('/register', [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'role' => 'Student',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('dashboard', absolute: false));
    expect(User::where('email', 'test@example.com')->value('role'))->toBe(User::STUDENT);
});

test('users can register with an allowed role', function () {
    $response = $this->from('/register')->post('/register', [
        'name' => 'Attempted Administrator',
        'email' => 'attempt@example.com',
        'role' => 'Administrator',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $response->assertRedirect(route('dashboard', absolute: false));
    expect(User::where('email', 'attempt@example.com')->value('role'))->toBe(User::ADMINISTRATOR);
});

test('registration rejects an invalid role', function () {
    $response = $this->from('/register')->post('/register', [
        'name' => 'Invalid Role',
        'email' => 'invalid-role@example.com',
        'role' => 'Superuser',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $response->assertRedirect('/register');
    $this->assertGuest();
    $this->assertDatabaseMissing('users', ['email' => 'invalid-role@example.com']);
});
