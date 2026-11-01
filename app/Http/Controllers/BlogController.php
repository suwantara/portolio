<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\BuildsPortfolioSeo;
use App\Models\PortfolioSetting;
use App\Models\Post;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class BlogController extends Controller
{
    use BuildsPortfolioSeo;

    /**
     * Display the published blog posts.
     */
    public function index(): Response
    {
        $portfolio = PortfolioSetting::content();
        $posts = Post::query()
            ->published()
            ->latest('published_at')
            ->select([
                'id',
                'title',
                'slug',
                'category',
                'excerpt',
                'cover_image',
                'published_at',
                'reading_minutes',
            ])
            ->paginate(9)
            ->withQueryString();

        return Inertia::render('Blog/Index', [
            'portfolio' => $portfolio,
            'posts' => $posts,
            'seo' => $this->blogIndexSeo($portfolio, $posts),
        ]);
    }

    /**
     * Display a single published blog post.
     */
    public function show(string $slug): Response
    {
        $post = Post::query()
            ->published()
            ->where('slug', $slug)
            ->firstOrFail();

        $portfolio = PortfolioSetting::content();

        return Inertia::render('Blog/Show', [
            'portfolio' => $portfolio,
            'post' => [
                'title' => $post->title,
                'slug' => $post->slug,
                'category' => $post->category,
                'excerpt' => $post->excerpt ?: Str::limit(
                    trim(strip_tags($post->renderedContent())),
                    160,
                ),
                'cover_image' => $post->cover_image,
                'published_at' => $post->published_at?->toIso8601String(),
                'reading_minutes' => $post->reading_minutes,
                'html' => $post->renderedContent(),
            ],
            'seo' => $this->blogPostSeo($portfolio, $post),
        ]);
    }
}
