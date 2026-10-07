<?php

use App\Models\User;

test('only administrators can manage users', function () {
    $user = User::factory()->create(['role' => User::STUDENT]);

    $this->actingAs($user)->get(route('users.index'))->assertForbidden();
});

test('administrator can view all users and assign an existing role', function () {
    $admin = User::factory()->create(['role' => User::ADMINISTRATOR]);
    $student = User::factory()->create(['role' => User::STUDENT]);

    $this->actingAs($admin)
        ->get(route('users.index'))
        ->assertOk()
        ->assertSee($student->name)
        ->assertSee($student->email);

    $this->put(route('users.update', $student), [
        'name' => 'Updated User',
        'email' => $student->email,
        'role' => User::FACULTY_ADVISER,
        'workplace' => 'Main Campus',
    ])->assertRedirect(route('users.index', absolute: false));

    expect($student->refresh()->role)->toBe(User::FACULTY_ADVISER);
});

test('administrator must provide a password when creating a user', function () {
    $admin = User::factory()->create(['role' => User::ADMINISTRATOR]);

    $this->actingAs($admin)
        ->from(route('users.create'))
        ->post(route('users.store'), [
            'name' => 'New User',
            'email' => 'new-user@example.com',
            'role' => User::STUDENT,
            'workplace' => 'Main Campus',
        ])
        ->assertSessionHasErrors('password');

    $this->actingAs($admin)
        ->post(route('users.store'), [
            'name' => 'New User',
            'email' => 'new-user@example.com',
            'role' => User::STUDENT,
            'workplace' => 'Main Campus',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ])
        ->assertRedirect(route('users.index', absolute: false));

    $user = User::where('email', 'new-user@example.com')->firstOrFail();

    expect($user->password)->not->toBe('password123');
});

test('administrator cannot assign an unknown role', function () {
    $admin = User::factory()->create(['role' => User::ADMINISTRATOR]);
    $student = User::factory()->create(['role' => User::STUDENT]);

    $this->actingAs($admin)
        ->from(route('users.edit', $student))
        ->put(route('users.update', $student), [
            'name' => $student->name,
            'email' => $student->email,
            'role' => 'Unknown Role',
        ])
        ->assertSessionHasErrors('role');
});

test('administrator can delete another user but not their own account', function () {
    $admin = User::factory()->create(['role' => User::ADMINISTRATOR]);
    $student = User::factory()->create(['role' => User::STUDENT]);

    $this->actingAs($admin)
        ->delete(route('users.destroy', $student))
        ->assertRedirect(route('users.index', absolute: false));

    expect($student->fresh())->toBeNull();

    $this->delete(route('users.destroy', $admin))
        ->assertRedirect(route('users.index', absolute: false))
        ->assertSessionHas('error');

    expect($admin->fresh())->not->toBeNull();
});
