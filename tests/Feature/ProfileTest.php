<?php

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

test('profile page is displayed', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->get('/profile');

    $response
        ->assertOk()
        ->assertSee('name="profile_photo"', false)
        ->assertDontSee('name="avatar"', false)
        ->assertDontSee('name="role"', false)
        ->assertSee('Your assigned role is managed by a CCTAS administrator.');
});

test('profile information can be updated', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->patch('/profile', [
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect('/profile');

    $user->refresh();

    $this->assertSame('Test User', $user->name);
    $this->assertSame('test@example.com', $user->email);
    $this->assertNull($user->email_verified_at);
});

test('users cannot change their assigned role through profile settings', function () {
    $user = User::factory()->create([
        'role' => User::STUDENT,
    ]);

    $response = $this
        ->actingAs($user)
        ->patch('/profile', [
            'name' => $user->name,
            'email' => $user->email,
            'role' => User::ADMINISTRATOR,
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect('/profile');

    expect($user->refresh()->role)->toBe(User::STUDENT);
});

test('email verification status is unchanged when the email address is unchanged', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->patch('/profile', [
            'name' => 'Test User',
            'email' => $user->email,
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect('/profile');

    $this->assertNotNull($user->refresh()->email_verified_at);
});

test('user can delete their account', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->delete('/profile', [
            'password' => 'password',
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect('/');

    $this->assertGuest();
    $this->assertNull($user->fresh());
});

test('profile photo can be uploaded, replaced, and removed', function () {
    Storage::fake('public');

    $user = User::factory()->create();

    $this
        ->actingAs($user)
        ->patch('/profile', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'profile_photo' => UploadedFile::fake()->image('avatar.jpg', 100, 100),
        ])
        ->assertSessionHasNoErrors()
        ->assertRedirect('/profile');

    $user->refresh();
    $this->assertNotNull($user->profile_photo_path);
    $this->assertTrue(Storage::disk('public')->exists($user->profile_photo_path));
    $originalProfilePhotoPath = $user->profile_photo_path;

    $this
        ->actingAs($user)
        ->patch('/profile', [
            'name' => 'Test User',
            'email' => $user->email,
            'profile_photo' => UploadedFile::fake()->image('updated-avatar.png', 100, 100),
        ])
        ->assertSessionHasNoErrors()
        ->assertRedirect('/profile');

    $user->refresh();
    $this->assertNotNull($user->profile_photo_path);
    $this->assertNotSame($originalProfilePhotoPath, $user->profile_photo_path);
    $this->assertFalse(Storage::disk('public')->exists($originalProfilePhotoPath));
    $this->assertTrue(Storage::disk('public')->exists($user->profile_photo_path));
    $replacementProfilePhotoPath = $user->profile_photo_path;

    $this
        ->actingAs($user)
        ->patch('/profile', [
            'name' => $user->name,
            'email' => $user->email,
            'remove_profile_photo' => true,
        ])
        ->assertSessionHasNoErrors()
        ->assertRedirect('/profile');

    $user->refresh();
    $this->assertNull($user->profile_photo_path);
    $this->assertFalse(Storage::disk('public')->exists($replacementProfilePhotoPath));
});

test('correct password must be provided to delete account', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->from('/profile')
        ->delete('/profile', [
            'password' => 'wrong-password',
        ]);

    $response
        ->assertSessionHasErrorsIn('userDeletion', 'password')
        ->assertRedirect('/profile');

    $this->assertNotNull($user->fresh());
});
