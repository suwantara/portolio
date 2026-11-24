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
        @endphp

        <meta name="robots" content="{{ $seoRobots }}">

        <x-inertia::head>
            <title>{{ $seoTitle }}</title>
            @if ($seoDescription !== '')
                <meta name="description" content="{{ $seoDescription }}">
                <meta property="og:description" content="{{ $seoDescription }}">
                <meta name="twitter:description" content="{{ $seoDescription }}">
            @endif
            @if ($seoAuthor)
                <meta name="author" content="{{ $seoAuthor }}">
            @endif
            <link rel="canonical" href="{{ $seoCanonical }}">

            <meta property="og:type" content="{{ $seoType }}">
            <meta property="og:title" content="{{ $seoTitle }}">
            <meta property="og:url" content="{{ $seoCanonical }}">
            <meta property="og:site_name" content="{{ $seoSiteName }}">
            <meta property="og:image" content="{{ $seoImage }}">
            <meta property="og:image:secure_url" content="{{ $seoImage }}">
            <meta property="og:image:alt" content="{{ $seoTitle }}">
            <meta property="og:image:width" content="1200">
            <meta property="og:image:height" content="630">

            <meta name="twitter:card" content="summary_large_image">
            <meta name="twitter:title" content="{{ $seoTitle }}">
            <meta name="twitter:image" content="{{ $seoImage }}">
            <meta name="twitter:image:alt" content="{{ $seoTitle }}">

            <meta name="theme-color" media="(prefers-color-scheme: light)" content="#f7f8fa">
            <meta name="theme-color" media="(prefers-color-scheme: dark)" content="#07080c">
        </x-inertia::head>

        @if (is_array($seoSchema))
            <script type="application/ld+json">{!! json_encode($seoSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) !!}</script>
        @endif

        @fonts

        @vite(['resources/css/app.css', 'resources/js/app.ts'])
    </head>
    <body class="font-sans antialiased">
        <x-inertia::app />
    </body>
</html>
