<?php

use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

test('missing pages render the custom error page', function () {
    $this->get('/this-page-does-not-exist')
        ->assertNotFound()
        ->assertInertia(fn (Assert $page) => $page
            ->component('ErrorPage')
            ->where('status', 404)
        );
});

test('forbidden pages render the custom error page', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('blog.manage'))
        ->assertForbidden()
        ->assertInertia(fn (Assert $page) => $page
            ->component('ErrorPage')
            ->where('status', 403)
        );
});

test('json errors are not replaced with the Inertia error page', function () {
    $this->postJson(route('blog.media.store'), [])
        ->assertUnauthorized()
        ->assertJsonStructure(['message']);
});
