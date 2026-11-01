<?php

use App\Models\PortfolioSetting;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

test('guests cannot update the portfolio', function () {
    $this->put(route('portfolio.update'), config('portfolio.defaults'))
        ->assertRedirect(route('login'));
});

test('non admin users cannot open or update the portfolio', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('portfolio.edit'))
        ->assertForbidden();

    $this->actingAs($user)
        ->put(route('portfolio.update'), config('portfolio.defaults'))
        ->assertForbidden();

    expect(PortfolioSetting::query()->exists())->toBeFalse();
});

test('admins can open the portfolio editor', function () {
    $user = User::factory()->admin()->create();

    $this->actingAs($user)
        ->get(route('portfolio.edit'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('settings/Portfolio')
            ->where('portfolio.profile.name', config('portfolio.defaults.profile.name'))
            ->has('options.experience_accents')
        );
});

test('admins can update portfolio content', function () {
    $user = User::factory()->admin()->create();

    $content = config('portfolio.defaults');
    $content['profile']['name'] = 'Updated Name';
    $content['socials'][0]['name'] = 'discord';
    $content['socials'][0]['href'] = 'https://discord.com/users/xuan';

    $this->actingAs($user)
        ->put(route('portfolio.update'), $content)
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('portfolio.edit'));

    $saved = PortfolioSetting::content();

    expect($saved['profile']['name'])->toBe('Updated Name');
    expect($saved['socials'][0]['name'])->toBe('discord');
    expect($saved['socials'][0]['href'])->toBe('https://discord.com/users/xuan');
});

test('portfolio update validates nested content', function () {
    $user = User::factory()->admin()->create();

    $content = config('portfolio.defaults');
    $content['profile']['name'] = '';
    $content['profile']['email'] = 'not-an-email';
    $content['profile']['availability_open'] = 'not-a-boolean';
    $content['experience'][0]['accent'] = 'neon';
    $content['projects'][0]['featured'] = 'not-a-boolean';
    $content['socials'][0]['href'] = 'not-a-url';
    $content['gallery'][0]['image'] = 'javascript:alert(1)';
    $content['documents'] = [
        ['title' => 'CV', 'description' => '', 'file' => 'not-a-file'],
    ];

    $this->actingAs($user)
        ->put(route('portfolio.update'), $content)
        ->assertSessionHasErrors([
            'profile.name',
            'profile.email',
            'profile.availability_open',
            'experience.0.accent',
            'projects.0.featured',
            'socials.0.href',
            'gallery.0.image',
            'documents.0.file',
        ]);

    expect(PortfolioSetting::query()->exists())->toBeFalse();
});
