<?php

use App\Models\PortfolioSetting;

test('portfolio content is escaped on the public page', function () {
    $content = config('portfolio.defaults');
    $content['profile']['name'] = 'Xuan <script>alert(1)</script>';
    $content['profile']['tagline'] = '<img src=x onerror=alert(1)>';

    PortfolioSetting::updateContent($content);

    $response = $this->get(route('home'));

    $response->assertOk();
    $response->assertDontSee('<script>alert(1)</script>', false);
    $response->assertDontSee('<img src=x onerror=alert(1)>', false);
    $response->assertSee('&lt;script&gt;alert(1)&lt;/script&gt;', false);
});
