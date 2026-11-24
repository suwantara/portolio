<?php

namespace App\Support;

use Illuminate\Support\Str;

class ProjectContent
{
    /**
     * Normalize project slugs and gallery values.
     *
     * @param  array<int, mixed>  $projects
     * @return array<int, array<string, mixed>>
     */
    public static function normalize(array $projects): array
    {
        $normalized = [];
        $usedSlugs = [];

        foreach ($projects as $project) {
            if (! is_array($project)) {
                continue;
            }

            $base = self::baseSlug($project);
            $slug = $base;
            $suffix = 2;

            while (in_array($slug, $usedSlugs, true)) {
                $slug = $base.'-'.$suffix;
                $suffix++;
            }

            $usedSlugs[] = $slug;
            $project['slug'] = $slug;
            $project['gallery'] = self::gallery($project['gallery'] ?? []);

            $normalized[] = $project;
        }

        return $normalized;
    }

    /**
     * Find a project by its normalized slug.
     *
     * @param  array<int, array<string, mixed>>  $projects
     * @return array<string, mixed>|null
     */
    public static function find(array $projects, string $slug): ?array
    {
        foreach ($projects as $project) {
            if (($project['slug'] ?? null) === $slug) {
                return $project;
            }
        }

        return null;
    }

    /**
     * Get a URL-safe slug base from the saved slug or project name.
     *
     * @param  array<string, mixed>  $project
     */
    private static function baseSlug(array $project): string
    {
        $savedSlug = $project['slug'] ?? null;
        $slug = is_string($savedSlug) ? Str::slug($savedSlug) : '';

        if ($slug !== '') {
            return $slug;
        }

        $name = $project['name'] ?? null;
        $slug = is_string($name) ? Str::slug($name) : '';

        return $slug !== '' ? $slug : 'project';
    }

    /**
     * Keep only non-empty image URLs.
     *
     * @return array<int, string>
     */
    private static function gallery(mixed $gallery): array
    {
        if (! is_array($gallery)) {
            return [];
        }

        $images = [];

        foreach ($gallery as $image) {
            if (is_string($image) && trim($image) !== '') {
                $images[] = trim($image);
            }
        }

        return array_values(array_unique($images));
    }
}
