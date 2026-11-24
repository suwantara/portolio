<?php

namespace App\Http\Controllers\Concerns;

use App\Models\Post;
use Illuminate\Support\Str;

trait BuildsPortfolioSeo
{
    private const BRAND_NAME = 'Xuanjinyu';

    private const LEGAL_NAME = 'I Wayan Suwantara Putra';

    private const JOB_TITLE = 'Full-Stack Developer & Graphic Designer';

    private const HOME_TITLE = 'Xuanjinyu — I Wayan Suwantara Putra | Full-Stack Developer & Graphic Designer';

    /**
     * @var array<int, string>
     */
    private const KNOWS_ABOUT = [
        'Full-Stack Web Development',
        'Laravel',
        'Svelte',
        'UI/UX Design',
        'Graphic Design',
    ];

    /**
     * Build the SEO payload for the home page.
     *
     * @param  array<string, mixed>  $portfolio
     * @return array<string, mixed>
     */
    protected function homeSeo(array $portfolio): array
    {
        $profile = $this->arrayValue($portfolio, 'profile');
        $base = $this->portfolioBaseUrl();
        $url = $base.'/';

        $tagline = $this->stringValue($profile, 'tagline');
        $location = $this->stringValue($profile, 'location');
        $email = $this->stringValue($profile, 'email');
        $about = $this->stringList($portfolio, 'about');

        $title = self::HOME_TITLE;
        $description = $tagline !== '' ? $tagline : 'Portfolio of '.self::LEGAL_NAME.'.';
        $ogImage = $this->ogImage($base);
        $avatar = $this->absoluteAsset(
            $this->stringValue($profile, 'avatar'),
            $base,
        );
        $sameAs = $this->socialUrls($portfolio);

        if (! $this->hasLinkedInUrl($sameAs)) {
            $sameAs[] = 'https://linkedin.com';
        }

        $languages = array_column($this->arrayList($portfolio, 'languages'), 'name');
        $education = array_column($this->arrayList($portfolio, 'education'), 'school');
        $projects = $this->arrayList($portfolio, 'projects');

        $person = $this->withoutEmptyValues([
            '@type' => 'Person',
            '@id' => $url.'#person',
            'name' => self::LEGAL_NAME,
            'alternateName' => [self::BRAND_NAME, 'Xuan'],
            'jobTitle' => self::JOB_TITLE,
            'description' => trim(implode(' ', array_slice($about, 0, 2))),
            'url' => $url,
            'image' => $avatar !== '' ? $avatar : $ogImage,
            'email' => $email !== '' ? 'mailto:'.$email : null,
            'address' => $location !== ''
                ? ['@type' => 'PostalAddress', 'addressLocality' => $location]
                : null,
            'sameAs' => $sameAs,
            'knowsAbout' => self::KNOWS_ABOUT,
            'knowsLanguage' => array_values(array_filter($languages, 'is_string')),
            'alumniOf' => array_values(array_map(
                fn (mixed $school): array => [
                    '@type' => 'EducationalOrganization',
                    'name' => (string) $school,
                ],
                array_filter($education, 'is_string'),
            )),
        ]);

        $graph = [
            [
                '@type' => 'WebSite',
                '@id' => $url.'#website',
                'url' => $url,
                'name' => self::BRAND_NAME,
                'alternateName' => [self::LEGAL_NAME],
                'description' => $description,
                'inLanguage' => str_replace('_', '-', app()->getLocale()),
            ],
            $person,
            [
                '@type' => 'ProfilePage',
                '@id' => $url.'#profile',
                'url' => $url,
                'name' => $title,
                'description' => $description,
                'isPartOf' => ['@id' => $url.'#website'],
                'mainEntity' => ['@id' => $url.'#person'],
            ],
        ];

        if ($projects !== []) {
            $graph[] = [
                '@type' => 'ItemList',
                'name' => 'Selected work',
                'itemListElement' => $this->projectListItems($projects, $base, $url.'#work'),
            ];
        }

        return [
            'title' => $title,
            'description' => $description,
            'canonical' => $url,
            'image' => $ogImage,
            'type' => 'profile',
            'robots' => 'index, follow, max-image-preview:large',
            'site_name' => self::BRAND_NAME,
            'author' => self::LEGAL_NAME,
            'schema' => [
                '@context' => 'https://schema.org',
                '@graph' => $graph,
            ],
        ];
    }

    /**
     * Build the SEO payload for the full work index.
     *
     * @param  array<string, mixed>  $portfolio
     * @return array<string, mixed>
     */
    protected function workSeo(array $portfolio): array
    {
        $base = $this->portfolioBaseUrl();
        $header = $this->arrayValue($portfolio, 'work_header');
        $projects = $this->arrayList($portfolio, 'projects');
        $title = $this->stringValue($header, 'title', 'Work').' — '.self::BRAND_NAME;
        $description = str_replace(
            '{count}',
            (string) count($projects),
            $this->stringValue(
                $header,
                'description',
                'Projects and case studies by '.self::BRAND_NAME.'.',
            ),
        );
        $url = $base.'/work';

        return [
            'title' => $title,
            'description' => $description,
            'canonical' => $url,
            'image' => $this->ogImage($base),
            'type' => 'website',
            'robots' => 'index, follow, max-image-preview:large',
            'site_name' => self::BRAND_NAME,
            'author' => self::LEGAL_NAME,
            'schema' => $this->withoutEmptyValues([
                '@context' => 'https://schema.org',
                '@type' => 'CollectionPage',
                'name' => $title,
                'description' => $description,
                'url' => $url,
                'mainEntity' => [
                    '@type' => 'ItemList',
                    'itemListElement' => $this->projectListItems(
                        $projects,
                        $base,
                        $url,
                    ),
                ],
                'author' => [
                    '@type' => 'Person',
                    'name' => self::LEGAL_NAME,
                    'url' => $base.'/',
                ],
            ]),
        ];
    }

    /**
     * Build the SEO payload for a project case study.
     *
     * @param  array<string, mixed>  $portfolio
     * @param  array<string, mixed>  $project
     * @return array<string, mixed>
     */
    protected function projectSeo(array $portfolio, array $project): array
    {
        $base = $this->portfolioBaseUrl();
        $slug = $this->stringValue($project, 'slug');
        $url = $base.'/work/'.$slug;
        $name = $this->stringValue($project, 'name', 'Project');
        $description = Str::limit(
            trim(strip_tags($this->stringValue($project, 'description'))),
            160,
        );
        $images = [];

        foreach (array_merge(
            [$this->stringValue($project, 'image')],
            $this->stringList($project, 'gallery'),
        ) as $asset) {
            $image = $this->absoluteAsset($asset, $base);

            if ($image !== '') {
                $images[] = $image;
            }
        }

        $images = array_values(array_unique($images));
        $image = $images[0] ?? $this->ogImage($base);
        $title = $name.' — '.self::BRAND_NAME;

        return [
            'title' => $title,
            'description' => $description !== ''
                ? $description
                : 'A project case study by '.self::BRAND_NAME.'.',
            'canonical' => $url,
            'image' => $image,
            'type' => 'article',
            'robots' => 'index, follow, max-image-preview:large',
            'site_name' => self::BRAND_NAME,
            'author' => self::LEGAL_NAME,
            'schema' => $this->withoutEmptyValues([
                '@context' => 'https://schema.org',
                '@type' => 'CreativeWork',
                'name' => $name,
                'description' => $description,
                'image' => $images === [] ? $image : $images,
                'url' => $url,
                'dateCreated' => $this->stringValue($project, 'year'),
                'keywords' => $this->stringList($project, 'tags'),
                'author' => [
                    '@type' => 'Person',
                    'name' => self::LEGAL_NAME,
                    'url' => $base.'/',
                ],
                'creator' => [
                    '@type' => 'Person',
                    'name' => self::LEGAL_NAME,
                    'url' => $base.'/',
                ],
            ]),
        ];
    }

    /**
     * Build the SEO payload for the gallery page.
     *
     * @param  array<string, mixed>  $portfolio
     * @return array<string, mixed>
     */
    protected function gallerySeo(array $portfolio): array
    {
        $base = $this->portfolioBaseUrl();
        $header = $this->arrayValue($portfolio, 'gallery_header');
        $galleryItems = $this->arrayList($portfolio, 'gallery');
        $title = $this->stringValue($header, 'title', 'Gallery').' — '.self::BRAND_NAME;
        $description = str_replace(
            '{count}',
            (string) count($galleryItems),
            $this->stringValue(
                $header,
                'description',
                'Selected images and visual studies by '.self::BRAND_NAME.'.',
            ),
        );

        $images = [];

        foreach (array_slice($galleryItems, 0, 30) as $item) {
            $source = $this->absoluteAsset($this->stringValue($item, 'image'), $base);

            if ($source === '') {
                continue;
            }

            $images[] = [
                '@type' => 'ImageObject',
                'contentUrl' => $source,
                'url' => $source,
                'name' => $this->stringValue($item, 'title'),
                'description' => $this->stringValue($item, 'caption'),
            ];
        }

        $ogImage = $this->ogImage($base);

        return [
            'title' => $title,
            'description' => $description,
            'canonical' => $base.'/gallery',
            'image' => $ogImage,
            'type' => 'website',
            'robots' => 'index, follow, max-image-preview:large',
            'site_name' => self::BRAND_NAME,
            'author' => self::LEGAL_NAME,
            'schema' => [
                '@context' => 'https://schema.org',
                '@type' => 'ImageGallery',
                'name' => $title,
                'description' => $description,
                'url' => $base.'/gallery',
                'image' => $images,
                'author' => [
                    '@type' => 'Person',
                    'name' => self::LEGAL_NAME,
                    'url' => $base.'/',
                ],
            ],
        ];
    }

    /**
     * Build the SEO payload for the blog index.
     *
     * @param  array<string, mixed>  $portfolio
     * @param  iterable<int, Post>  $posts
     * @return array<string, mixed>
     */
    protected function blogIndexSeo(array $portfolio, iterable $posts): array
    {
        $base = $this->portfolioBaseUrl();
        $header = $this->arrayValue($portfolio, 'blog_header');
        $title = $this->stringValue($header, 'title', 'Blog').' — '.self::BRAND_NAME;
        $description = $this->stringValue(
            $header,
            'description',
            'Tutorials, tips, and build notes by '.self::BRAND_NAME.'.',
        );
        $url = $base.'/blog';
        $items = [];

        foreach ($posts as $post) {
            $items[] = $this->withoutEmptyValues([
                '@type' => 'BlogPosting',
                'headline' => $post->title,
                'url' => $base.'/blog/'.$post->slug,
                'datePublished' => $post->published_at?->toAtomString(),
            ]);
        }

        return [
            'title' => $title,
            'description' => $description,
            'canonical' => $url,
            'image' => $this->ogImage($base),
            'type' => 'website',
            'robots' => 'index, follow, max-image-preview:large',
            'site_name' => self::BRAND_NAME,
            'author' => self::LEGAL_NAME,
            'schema' => $this->withoutEmptyValues([
                '@context' => 'https://schema.org',
                '@type' => 'Blog',
                'name' => $title,
                'description' => $description,
                'url' => $url,
                'inLanguage' => str_replace('_', '-', app()->getLocale()),
                'blogPost' => $items,
                'author' => [
                    '@type' => 'Person',
                    'name' => self::LEGAL_NAME,
                    'url' => $base.'/',
                ],
            ]),
        ];
    }

    /**
     * Build the SEO payload for a blog post.
     *
     * @param  array<string, mixed>  $portfolio
     * @return array<string, mixed>
     */
    protected function blogPostSeo(array $portfolio, Post $post): array
    {
        $base = $this->portfolioBaseUrl();
        $baseUrl = $base.'/blog/'.$post->slug;
        $title = $post->title.' — '.self::BRAND_NAME;
        $description = $post->excerpt ?: Str::limit(
            trim(strip_tags($post->renderedContent())),
            160,
        );
        $cover = $this->absoluteAsset((string) $post->cover_image, $base);
        $image = $cover !== '' ? $cover : $this->ogImage($base);

        return [
            'title' => $title,
            'description' => $description,
            'canonical' => $baseUrl,
            'image' => $image,
            'type' => 'article',
            'robots' => 'index, follow, max-image-preview:large',
            'site_name' => self::BRAND_NAME,
            'author' => self::LEGAL_NAME,
            'schema' => $this->withoutEmptyValues([
                '@context' => 'https://schema.org',
                '@type' => 'BlogPosting',
                'headline' => $post->title,
                'description' => $description,
                'image' => $image,
                'url' => $baseUrl,
                'mainEntityOfPage' => [
                    '@type' => 'WebPage',
                    '@id' => $baseUrl,
                ],
                'datePublished' => $post->published_at?->toAtomString(),
                'dateModified' => $post->updated_at?->toAtomString(),
                'inLanguage' => str_replace('_', '-', app()->getLocale()),
                'author' => [
                    '@type' => 'Person',
                    'name' => self::LEGAL_NAME,
                    'url' => $base.'/',
                ],
                'publisher' => [
                    '@type' => 'Organization',
                    'name' => self::BRAND_NAME,
                ],
            ]),
        ];
    }

    /**
     * Build the canonical base URL for the portfolio.
     */
    protected function portfolioBaseUrl(): string
    {
        return rtrim((string) config('app.url', 'http://localhost'), '/');
    }

    /**
     * Get the absolute URL for the default social image.
     */
    protected function ogImage(string $base): string
    {
        $path = public_path('images/og/portfolio.png');
        $version = is_file($path) ? filemtime($path) : false;

        return $base.'/images/og/portfolio.png'.($version !== false ? '?v='.$version : '');
    }

    /**
     * Convert a local or remote asset reference to an absolute URL.
     */
    protected function absoluteAsset(string $value, string $base): string
    {
        if ($value === '' || trim($value) !== $value) {
            return '';
        }

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

    /**
     * Get the social profile URLs used for schema.org sameAs.
     *
     * @param  array<string, mixed>  $portfolio
     * @return array<int, string>
     */
    protected function socialUrls(array $portfolio): array
    {
        $urls = [];

        foreach ($this->arrayList($portfolio, 'socials') as $social) {
            $href = $this->stringValue($social, 'href');

            if ($href !== '' && $this->stringValue($social, 'name') !== 'website') {
                $urls[] = $href;
            }
        }

        return array_values(array_unique($urls));
    }

    /**
     * Check whether the social URLs include a LinkedIn profile.
     *
     * @param  array<int, string>  $urls
     */
    private function hasLinkedInUrl(array $urls): bool
    {
        foreach ($urls as $url) {
            if (str_contains(strtolower($url), 'linkedin.com')) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get the skill names used for schema.org knowsAbout.
     *
     * @param  array<string, mixed>  $portfolio
     * @return array<int, string>
     */
    protected function skillNames(array $portfolio): array
    {
        $skills = [];

        foreach ($this->arrayList($portfolio, 'skill_groups') as $group) {
            $skills = [
                ...$skills,
                ...$this->stringList($group, 'skills'),
            ];
        }

        return array_values(array_unique(array_slice($skills, 0, 30)));
    }

    /**
     * Build schema.org list items for selected projects.
     *
     * @param  array<int, array<string, mixed>>  $projects
     * @return array<int, array<string, mixed>>
     */
    protected function projectListItems(array $projects, string $base, string $fallback): array
    {
        $items = [];

        foreach (array_slice($projects, 0, 10) as $index => $project) {
            $slug = $this->stringValue($project, 'slug');
            $detailUrl = $slug !== '' ? $base.'/work/'.$slug : '';
            $link = $this->absoluteAsset(
                $this->stringValue($project, 'link'),
                $base,
            );

            $items[] = [
                '@type' => 'ListItem',
                'position' => $index + 1,
                'item' => [
                    '@type' => 'CreativeWork',
                    'name' => $this->stringValue($project, 'name'),
                    'description' => $this->stringValue($project, 'description'),
                    'url' => $detailUrl !== ''
                        ? $detailUrl
                        : ($link !== '' ? $link : $fallback),
                ],
            ];
        }

        return $items;
    }

    /**
     * Get an array value from a content payload.
     *
     * @param  array<string, mixed>  $source
     * @return array<string, mixed>
     */
    protected function arrayValue(array $source, string $key): array
    {
        $value = $source[$key] ?? null;

        return is_array($value) ? $value : [];
    }

    /**
     * Get a list of array values from a content payload.
     *
     * @param  array<string, mixed>  $source
     * @return array<int, array<string, mixed>>
     */
    protected function arrayList(array $source, string $key): array
    {
        $items = $this->arrayValue($source, $key);
        $result = [];

        foreach ($items as $item) {
            if (is_array($item)) {
                $result[] = $item;
            }
        }

        return $result;
    }

    /**
     * Get a list of string values from a content payload.
     *
     * @param  array<string, mixed>  $source
     * @return array<int, string>
     */
    protected function stringList(array $source, string $key): array
    {
        $items = $this->arrayValue($source, $key);

        return array_values(array_filter($items, 'is_string'));
    }

    /**
     * Get a string value from a content payload.
     *
     * @param  array<string, mixed>  $source
     */
    protected function stringValue(array $source, string $key, string $default = ''): string
    {
        $value = $source[$key] ?? null;

        return is_string($value) ? $value : $default;
    }

    /**
     * Remove null, empty string, and empty list values from a schema fragment.
     *
     * @param  array<string, mixed>  $values
     * @return array<string, mixed>
     */
    protected function withoutEmptyValues(array $values): array
    {
        return array_filter($values, fn (mixed $value): bool => $value !== null && $value !== '' && $value !== []);
    }
}
