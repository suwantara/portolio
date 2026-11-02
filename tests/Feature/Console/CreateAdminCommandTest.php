<?php

use App\Models\User;

test('it creates an administrator', function () {
    $this->artisan('app:create-admin', [
        'email' => 'admin@example.com',
        '--name' => 'Site Admin',
        '--password' => 'Password123!',
    ])->assertSuccessful();

    $user = User::where('email', 'admin@example.com')->firstOrFail();

    expect($user->name)->toBe('Site Admin')
        ->and($user->is_admin)->toBeTrue()
        ->and($user->email_verified_at)->not->toBeNull();
});

test('it rejects a duplicate email address', function () {
    User::factory()->create(['email' => 'admin@example.com']);

    $this->artisan('app:create-admin', [
        'email' => 'admin@example.com',
        '--password' => 'Password123!',
    ])->assertFailed();

    expect(User::where('email', 'admin@example.com')->count())->toBe(1);
});

test('it rejects a weak password', function () {
    $this->artisan('app:create-admin', [
        'email' => 'admin@example.com',
        '--password' => 'password',
    ])->assertFailed();

    expect(User::where('email', 'admin@example.com')->exists())->toBeFalse();
});
