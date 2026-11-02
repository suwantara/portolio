<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;

class RobotsController extends Controller
{
    /**
     * Display the robots.txt file.
     */
    public function __invoke(): Response
    {
        $base = rtrim((string) config('app.url', 'http://localhost'), '/');
        $disallowed = [
            'Disallow: /settings/',
            'Disallow: /login',
            'Disallow: /forgot-password',
            'Disallow: /reset-password',
            'Disallow: /confirm-password',
        ];

        $lines = ['User-agent: *', 'Allow: /', ...$disallowed, ''];

        foreach ([
            'GPTBot',
            'ChatGPT-User',
            'ClaudeBot',
            'Claude-Web',
            'PerplexityBot',
            'Google-Extended',
            'CCBot',
        ] as $bot) {
            $lines = [...$lines, 'User-agent: '.$bot, 'Allow: /', ...$disallowed, ''];
        }

        $lines = [
            ...$lines,
            'Sitemap: '.$base.'/sitemap.xml',
            '# LLM-friendly summary: '.$base.'/llms.txt',
        ];

        return response(implode("\n", $lines)."\n", 200, [
            'Content-Type' => 'text/plain; charset=UTF-8',
        ]);
    }
}
