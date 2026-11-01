<?php

use App\Models\PortfolioSetting;
use Inertia\Testing\AssertableInertia as Assert;

test('portfolio page renders the default content', function () {
    $this->get(route('home'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Welcome')
            ->where('portfolio.profile.name', config('portfolio.defaults.profile.name'))
            ->has('portfolio.experience', 3)
            ->has('portfolio.gallery', 3)
        );
});

test('portfolio content fills new profile defaults for older saved content', function () {
    $content = config('portfolio.defaults');
    unset($content['profile']['availability_note'], $content['profile']['availability_open']);

    PortfolioSetting::updateContent($content);

    $saved = PortfolioSetting::content();

    expect($saved['profile']['availability_note'])->toBe(
        config('portfolio.defaults.profile.availability_note'),
    );
    expect($saved['profile']['availability_open'])->toBeTrue();
});

test('portfolio page renders saved content', function () {
    $content = config('portfolio.defaults');
    $content['profile']['name'] = 'Saved Name';

    PortfolioSetting::updateContent($content);

    $this->get(route('home'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Welcome')
            ->where('portfolio.profile.name', 'Saved Name')
        );
});
