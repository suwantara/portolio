<?php

use App\Models\PortfolioSetting;
use Inertia\Testing\AssertableInertia as Assert;

test('gallery page renders the default images', function () {
    $this->get(route('gallery'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Gallery')
            ->where('portfolio.profile.name', config('portfolio.defaults.profile.name'))
            ->has('portfolio.gallery', 3)
        );
});

test('gallery page renders saved images', function () {
    $content = config('portfolio.defaults');
    $content['gallery'] = [
        [
            'image' => '/images/portfolio/gallery/atlas.svg',
            'title' => 'Saved frame',
            'caption' => 'A saved gallery image.',
            'link' => 'https://example.com',
            'featured' => true,
        ],
    ];

    PortfolioSetting::updateContent($content);

    $this->get(route('gallery'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Gallery')
            ->where('portfolio.gallery.0.title', 'Saved frame')
        );
});

test('gallery header copy is editable from portfolio content', function () {
    $content = config('portfolio.defaults');
    $content['gallery_header']['title'] = 'Selected frames';
    $content['gallery_header']['description'] = 'A custom header with {count} images.';

    PortfolioSetting::updateContent($content);

    $this->get(route('gallery'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->where('portfolio.gallery_header.title', 'Selected frames')
            ->where('portfolio.gallery_header.description', 'A custom header with {count} images.')
        );
});
