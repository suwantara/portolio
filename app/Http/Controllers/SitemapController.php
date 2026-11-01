<?php

namespace App\Http\Controllers;

use App\Models\PortfolioSetting;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    /**
     * Display the XML sitemap.
     */
    public function __invoke(): Response
    {
        $content = PortfolioSetting::content();
        $base = rtrim((string) config('app.url', 'http://localhost'), '/');
        $setting = PortfolioSetting::query()->first();
        $lastModified = $setting?->updated_at?->toAtomString()
            ?? now()->toAtomString();

        $urls = [
            [
                'loc' => $base.'/',
                'lastmod' => $lastModified,
                'changefreq' => 'weekly',
                'priority' => '1.0',
                'images' => [],
            ],
        ];

        $images = $this->galleryImages($content, $base);

        if ($images !== []) {
            $urls[] = [
                'loc' => $base.'/gallery',
                'lastmod' => $lastModified,
                'changefreq' => 'weekly',
                'priority' => '0.8',
                'images' => $images,
            ];
        }

        return response()->view('sitemap', ['urls' => $urls], 200, [
            'Content-Type' => 'application/xml; charset=UTF-8',
        ]);
    }

    /**
     * Build image sitemap entries from the gallery content.
     *
     * @param  array<string, mixed>  $content
     * @return array<int, array<string, string>>
     */
    private function galleryImages(array $content, string $base): array
    {
        $gallery = $content['gallery'] ?? null;
        $images = [];

        if (! is_array($gallery)) {
            return $images;
        }

        foreach ($gallery as $item) {
            if (! is_array($item)) {
                continue;
            }

            $path = $item['image'] ?? null;

            if (! is_string($path) || $path === '') {
                continue;
            }

            $location = $this->absoluteAsset($path, $base);

            if ($location === '') {
                continue;
            }

            $images[] = [
                'loc' => $location,
                'title' => is_string($item['title'] ?? null) ? $item['title'] : '',
                'caption' => is_string($item['caption'] ?? null) ? $item['caption'] : '',
            ];
        }

        return $images;
    }

    /**
     * Convert a local or remote asset reference to an absolute URL.
     */
    private function absoluteAsset(string $value, string $base): string
    {
        if (str_starts_with($value, '/') && ! str_starts_with($value, '//')) {
            return $base.$value;
        }

        $scheme = parse_url($value, PHP_URL_SCHEME);

        if (
            filter_var($value, FILTER_VALIDATE_URL) !== false
            && in_array(strtolower((string) $scheme), ['http', 'https'], true)
        ) {
            return $value;
        }

        return '';
    }
}
