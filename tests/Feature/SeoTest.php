<?php

test('home page exposes search and social metadata', function () {
    $this->get(route('home'))
        ->assertOk()
        ->assertSee('<title>Xuan Jinyu — Full-Stack Developer &amp; Design Engineer</title>', false)
        ->assertSee('<meta name="description"', false)
        ->assertSee('<link rel="canonical"', false)
        ->assertSee('<meta property="og:image"', false)
        ->assertSee('<meta property="og:image:secure_url"', false)
        ->assertSee('application/ld+json', false)
        ->assertSee('ProfilePage', false)
        ->assertSee('max-image-preview:large', false);
});

test('gallery page exposes its own canonical and image gallery schema', function () {
    $this->get(route('gallery'))
        ->assertOk()
        ->assertSee('<link rel="canonical" href="'.rtrim((string) config('app.url'), '/').'/gallery"', false)
        ->assertSee('ImageGallery', false)
        ->assertSee('atlas.svg', false)
        ->assertSee('index, follow', false);
});

test('private pages are marked noindex', function () {
    $this->get(route('login'))
        ->assertOk()
        ->assertSee('<meta name="robots" content="noindex, nofollow">', false);
});

test('sitemap lists the public pages and gallery images', function () {
    $base = rtrim((string) config('app.url'), '/');

    $this->get(route('sitemap'))
        ->assertOk()
        ->assertHeader('Content-Type', 'application/xml; charset=UTF-8')
        ->assertSee('<loc>'.$base.'/</loc>', false)
        ->assertSee('<loc>'.$base.'/gallery</loc>', false)
        ->assertSee('<image:loc>'.$base.'/images/portfolio/gallery/atlas.svg</image:loc>', false);
});

test('robots allows public pages and points to the sitemap', function () {
    $base = rtrim((string) config('app.url'), '/');

    $this->get(route('robots'))
        ->assertOk()
        ->assertHeader('Content-Type', 'text/plain; charset=UTF-8')
        ->assertSee('Disallow: /settings/', false)
        ->assertSee('Sitemap: '.$base.'/sitemap.xml', false)
        ->assertSee('User-agent: GPTBot', false);
});

test('llms txt provides a factual portfolio summary', function () {
    $base = rtrim((string) config('app.url'), '/');

    $this->get(route('llms'))
        ->assertOk()
        ->assertHeader('Content-Type', 'text/plain; charset=UTF-8')
        ->assertSee('# Xuan Jinyu', false)
        ->assertSee('Gallery: '.$base.'/gallery', false)
        ->assertSee('## Experience', false)
        ->assertSee('## Selected work', false)
        ->assertSee('Universitas Nusantara', false)
        ->assertSee('Sitemap: '.$base.'/sitemap.xml', false);
});
