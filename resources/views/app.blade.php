<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"  @class(['dark' => ($appearance ?? 'system') == 'dark'])>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <link rel="icon" href="/favicon.ico" sizes="any">
        <link rel="icon" href="/favicon.svg" type="image/svg+xml">
        <link rel="apple-touch-icon" href="/apple-touch-icon.png">
        <link rel="manifest" href="/site.webmanifest">
        <link rel="mask-icon" href="/safari-pinned-tab.svg" color="#0a66c2">
        <meta name="msapplication-TileColor" content="#0a66c2">

        @php
            $seo = is_array($page['props']['seo'] ?? null) ? $page['props']['seo'] : [];
            $component = (string) ($page['component'] ?? '');
            $defaultRobots = str_starts_with($component, 'auth/')
                || str_starts_with($component, 'settings/')
                ? 'noindex, nofollow'
                : 'index, follow';
            $seoTitle = $seo['title'] ?? config('app.name', 'Laravel');
            $seoDescription = $seo['description'] ?? '';
            $seoCanonical = $seo['canonical'] ?? url()->current();
            $seoImage = $seo['image'] ?? asset('images/og/portfolio.png');
            $seoType = $seo['type'] ?? 'website';
            $seoRobots = $seo['robots'] ?? $defaultRobots;
            $seoSiteName = $seo['site_name'] ?? config('app.name', 'Laravel');
            $seoAuthor = $seo['author'] ?? null;
            $seoSchema = $seo['schema'] ?? null;

            $portfolio = is_array($page['props']['portfolio'] ?? null)
                ? $page['props']['portfolio']
                : [];
            $socials = is_array($portfolio['socials'] ?? null)
                ? $portfolio['socials']
                : [];
            $sameAs = [];

            foreach ($socials as $social) {
                $href = is_array($social) ? ($social['href'] ?? null) : null;

                if (is_string($href) && $href !== '') {
                    $sameAs[] = $href;
                }
            }

            $hasLinkedIn = false;

            foreach ($sameAs as $url) {
                if (str_contains(strtolower($url), 'linkedin.com')) {
                    $hasLinkedIn = true;
                    break;
                }
            }

            if (! $hasLinkedIn) {
                $sameAs[] = 'https://linkedin.com';
            }

            $fallbackSchema = [
                '@context' => 'https://schema.org',
                '@graph' => [
                    [
                        '@type' => 'WebSite',
                        '@id' => 'https://xuanjinyu.com/#website',
                        'url' => 'https://xuanjinyu.com',
                        'name' => 'Xuanjinyu',
                        'alternateName' => ['I Wayan Suwantara Putra'],
                        'inLanguage' => str_replace('_', '-', app()->getLocale()),
                    ],
                    [
                        '@type' => 'Person',
                        '@id' => 'https://xuanjinyu.com/#person',
                        'name' => 'I Wayan Suwantara Putra',
                        'alternateName' => ['Xuanjinyu', 'Xuan'],
                        'url' => 'https://xuanjinyu.com',
                        'jobTitle' => 'Full-Stack Developer & Graphic Designer',
                        'knowsAbout' => [
                            'Full-Stack Web Development',
                            'Laravel',
                            'Svelte',
                            'UI/UX Design',
                            'Graphic Design',
                        ],
                        'sameAs' => array_values(array_unique($sameAs)),
                    ],
                    [
                        '@type' => 'ProfilePage',
                        '@id' => 'https://xuanjinyu.com/#profile',
                        'url' => 'https://xuanjinyu.com',
                        'name' => 'Xuanjinyu — I Wayan Suwantara Putra | Full-Stack Developer & Graphic Designer',
                        'mainEntity' => [
                            '@id' => 'https://xuanjinyu.com/#person',
                        ],
                        'isPartOf' => [
                            '@id' => 'https://xuanjinyu.com/#website',
                        ],
                    ],
                ],
            ];
        @endphp

        <meta name="robots" content="{{ $seoRobots }}">

        <x-inertia::head>
            <title data-inertia-fallback inertia>{{ $seoTitle }}</title>
            @if ($seoDescription !== '')
                <meta data-inertia-fallback name="description" content="{{ $seoDescription }}">
                <meta data-inertia-fallback property="og:description" content="{{ $seoDescription }}">
                <meta data-inertia-fallback name="twitter:description" content="{{ $seoDescription }}">
            @endif
            @if ($seoAuthor)
                <meta data-inertia-fallback name="author" content="{{ $seoAuthor }}">
            @endif
            <link data-inertia-fallback rel="canonical" href="{{ $seoCanonical }}">

            <meta data-inertia-fallback property="og:type" content="{{ $seoType }}">
            <meta data-inertia-fallback property="og:title" content="{{ $seoTitle }}">
            <meta data-inertia-fallback property="og:url" content="{{ $seoCanonical }}">
            <meta data-inertia-fallback property="og:site_name" content="{{ $seoSiteName }}">
            <meta data-inertia-fallback property="og:image" content="{{ $seoImage }}">
            <meta data-inertia-fallback property="og:image:secure_url" content="{{ $seoImage }}">
            <meta data-inertia-fallback property="og:image:alt" content="{{ $seoTitle }}">
            <meta data-inertia-fallback property="og:image:width" content="1200">
            <meta data-inertia-fallback property="og:image:height" content="630">

            <meta data-inertia-fallback name="twitter:card" content="summary_large_image">
            <meta data-inertia-fallback name="twitter:title" content="{{ $seoTitle }}">
            <meta data-inertia-fallback name="twitter:image" content="{{ $seoImage }}">
            <meta data-inertia-fallback name="twitter:image:alt" content="{{ $seoTitle }}">

            <meta data-inertia-fallback name="theme-color" media="(prefers-color-scheme: light)" content="#f7f8fa">
            <meta data-inertia-fallback name="theme-color" media="(prefers-color-scheme: dark)" content="#07080c">
        </x-inertia::head>

        @if (is_array($seoSchema))
            <script type="application/ld+json">{!! json_encode($seoSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) !!}</script>
        @else
            <script type="application/ld+json">{!! json_encode($fallbackSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) !!}</script>
        @endif

        @fonts

        @vite(['resources/css/app.css', 'resources/js/app.ts'])
    </head>
    <body class="font-sans antialiased">
        <x-inertia::app />
    </body>
</html>
