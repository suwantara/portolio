@php echo '<?xml version="1.0" encoding="UTF-8"?>'; @endphp
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">
@foreach ($urls as $url)
    <url>
        <loc>{{ $url['loc'] }}</loc>
        <lastmod>{{ $url['lastmod'] }}</lastmod>
        <changefreq>{{ $url['changefreq'] }}</changefreq>
        <priority>{{ $url['priority'] }}</priority>
@foreach ($url['images'] as $image)
        <image:image>
            <image:loc>{{ $image['loc'] }}</image:loc>
@if ($image['title'] !== '')
            <image:title>{{ $image['title'] }}</image:title>
@endif
@if ($image['caption'] !== '')
            <image:caption>{{ $image['caption'] }}</image:caption>
@endif
        </image:image>
@endforeach
    </url>
@endforeach
</urlset>
