<?php

namespace App\Http\Controllers;

use App\Models\PortfolioSetting;
use Illuminate\Http\Response;

class LlmsTxtController extends Controller
{
    /**
     * Display an LLM-friendly summary of the portfolio.
     */
    public function __invoke(): Response
    {
        $content = PortfolioSetting::content();
        $base = rtrim((string) config('app.url', 'http://localhost'), '/');
        $profile = $this->arrayValue($content, 'profile');

        $name = $this->stringValue($profile, 'name', (string) config('app.name'));
        $role = $this->stringValue($profile, 'role');
        $tagline = $this->stringValue($profile, 'tagline');

        $intro = trim(($role !== '' ? $role.'. ' : '').$tagline);

        $lines = [
            '# '.$name,
            '',
            '> '.$intro,
            '',
            '## Contact',
            '',
            '- Portfolio: '.$base.'/',
            '- Gallery: '.$base.'/gallery',
            '- Blog: '.$base.'/blog',
            '- Email: '.$this->stringValue($profile, 'email'),
            '- Location: '.$this->stringValue($profile, 'location'),
            '',
        ];

        $about = $this->stringList($content, 'about');

        if ($about !== []) {
            $lines = [...$lines, '## About', '', implode("\n\n", $about), ''];
        }

        $experience = $this->arrayList($content, 'experience');

        if ($experience !== []) {
            $lines = [...$lines, '## Experience', ''];

            foreach ($experience as $job) {
                $line = '- **'.$this->stringValue($job, 'role').'** at '
                    .$this->stringValue($job, 'company')
                    .' ('.$this->stringValue($job, 'period').')';

                $description = $this->stringValue($job, 'description');

                if ($description !== '') {
                    $line .= ': '.$description;
                }

                $lines[] = $line;

                foreach ($this->stringList($job, 'highlights') as $highlight) {
                    $lines[] = '  - '.$highlight;
                }
            }

            $lines[] = '';
        }

        $projects = $this->arrayList($content, 'projects');

        if ($projects !== []) {
            $lines = [...$lines, '## Selected work', ''];

            foreach ($projects as $project) {
                $link = $this->absoluteAsset(
                    $this->stringValue($project, 'link'),
                    $base,
                    $base.'/#work',
                );

                $lines[] = '- ['.$this->stringValue($project, 'name').']('.$link.'): '
                    .$this->stringValue($project, 'description');
            }

            $lines[] = '';
        }

        $skillGroups = $this->arrayList($content, 'skill_groups');

        if ($skillGroups !== []) {
            $lines = [...$lines, '## Skills', ''];

            foreach ($skillGroups as $group) {
                $skills = $this->stringList($group, 'skills');

                if ($skills !== []) {
                    $lines[] = '- **'.$this->stringValue($group, 'title').'**: '.implode(', ', $skills);
                }
            }

            $lines[] = '';
        }

        $gallery = $this->arrayList($content, 'gallery');

        if ($gallery !== []) {
            $lines = [...$lines, '## Gallery', '', '- Full gallery: '.$base.'/gallery', ''];

            foreach (array_slice($gallery, 0, 10) as $item) {
                $image = $this->absoluteAsset($this->stringValue($item, 'image'), $base);

                if ($image === '') {
                    continue;
                }

                $line = '- ['.$this->stringValue($item, 'title').']('.$image.')';

                $caption = $this->stringValue($item, 'caption');

                if ($caption !== '') {
                    $line .= ': '.$caption;
                }

                $lines[] = $line;
            }

            $lines[] = '';
        }

        $documents = $this->arrayList($content, 'documents');

        if ($documents !== []) {
            $lines = [...$lines, '## Documents', ''];

            foreach ($documents as $document) {
                $file = $this->absoluteAsset($this->stringValue($document, 'file'), $base);

                if ($file === '') {
                    continue;
                }

                $line = '- ['.$this->stringValue($document, 'title').']('.$file.')';

                $description = $this->stringValue($document, 'description');

                if ($description !== '') {
                    $line .= ': '.$description;
                }

                $lines[] = $line;
            }

            $lines[] = '';
        }

        $education = $this->arrayList($content, 'education');

        if ($education !== []) {
            $lines = [...$lines, '## Education', ''];

            foreach ($education as $item) {
                $lines[] = '- '.$this->stringValue($item, 'degree').', '
                    .$this->stringValue($item, 'school')
                    .' ('.$this->stringValue($item, 'period').')';
            }

            $lines[] = '';
        }

        $languages = $this->arrayList($content, 'languages');

        if ($languages !== []) {
            $lines = [...$lines, '## Languages', ''];

            foreach ($languages as $language) {
                $lines[] = '- '.$this->stringValue($language, 'name')
                    .': '.$this->stringValue($language, 'level');
            }

            $lines[] = '';
        }

        $exploring = $this->stringList($content, 'currently_exploring');

        if ($exploring !== []) {
            $lines = [...$lines, '## Currently exploring', '', implode(', ', $exploring), ''];
        }

        $socials = $this->arrayList($content, 'socials');

        if ($socials !== []) {
            $lines = [...$lines, '## Social', ''];

            foreach ($socials as $social) {
                $href = $this->stringValue($social, 'href');

                if ($href !== '') {
                    $lines[] = '- ['.$this->stringValue($social, 'label').']('.$href.')';
                }
            }

            $lines[] = '';
        }

        $lines = [
            ...$lines,
            '## Machine-readable',
            '',
            '- Sitemap: '.$base.'/sitemap.xml',
            '- LLM summary: '.$base.'/llms.txt',
        ];

        return response(implode("\n", $lines)."\n", 200, [
            'Content-Type' => 'text/plain; charset=UTF-8',
        ]);
    }

    /**
     * Get an array value from a content payload.
     *
     * @param  array<string, mixed>  $source
     * @return array<string, mixed>
     */
    private function arrayValue(array $source, string $key): array
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
    private function arrayList(array $source, string $key): array
    {
        $result = [];

        foreach ($this->arrayValue($source, $key) as $item) {
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
    private function stringList(array $source, string $key): array
    {
        return array_values(array_filter($this->arrayValue($source, $key), 'is_string'));
    }

    /**
     * Get a string value from a content payload.
     *
     * @param  array<string, mixed>  $source
     */
    private function stringValue(array $source, string $key, string $default = ''): string
    {
        $value = $source[$key] ?? null;

        return is_string($value) ? $value : $default;
    }

    /**
     * Convert a local or remote asset reference to an absolute URL.
     */
    private function absoluteAsset(string $value, string $base, string $fallback = ''): string
    {
        if ($value === '') {
            return $fallback;
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

        return $fallback;
    }
}
