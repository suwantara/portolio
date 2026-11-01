<?php

use App\Models\User;

test('public responses include baseline security headers', function () {
    $this->get(route('home'))
        ->assertOk()
        ->assertHeader('X-Content-Type-Options', 'nosniff')
        ->assertHeader('X-Frame-Options', 'SAMEORIGIN')
        ->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin')
        ->assertHeader('Permissions-Policy', 'camera=(), microphone=(), geolocation=()')
        ->assertHeaderMissing('X-Powered-By');
});

test('authenticated responses include baseline security headers', function () {
    $user = User::factory()->admin()->create();

    $this->actingAs($user)
        ->get(route('portfolio.edit'))
        ->assertOk()
        ->assertHeader('X-Content-Type-Options', 'nosniff')
        ->assertHeader('X-Frame-Options', 'SAMEORIGIN');
});

test('error responses include baseline security headers', function () {
    $this->get('/this-page-does-not-exist')
        ->assertNotFound()
        ->assertHeader('X-Content-Type-Options', 'nosniff')
        ->assertHeader('X-Frame-Options', 'SAMEORIGIN')
        ->assertHeaderMissing('X-Powered-By');
});
