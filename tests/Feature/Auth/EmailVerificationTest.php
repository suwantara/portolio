<?php

use App\Models\User;

test('unverified admins can access the portfolio editor', function () {
    $user = User::factory()->admin()->unverified()->create();

    $this->actingAs($user)
        ->get(route('portfolio.edit'))
        ->assertOk();
});

test('email verification endpoints are disabled', function () {
    $this->get('/email/verify')->assertNotFound();
    $this->post('/email/verification-notification')->assertNotFound();
});
