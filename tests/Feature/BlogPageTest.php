<?php

use App\Models\PortfolioSetting;
use App\Models\Post;
use Inertia\Testing\AssertableInertia as Assert;

test('blog index only lists published posts', function () {
    $published = Post::factory()->published()->create([
        'title' => 'A published tutorial',
    ]);
    Post::factory()->create(['title' => 'A draft']);

    $this->get(route('blog.index'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Blog/Index')
            ->has('posts.data', 1)
            ->where('posts.data.0.title', $published->title)
            ->where('seo.canonical', rtrim((string) config('app.url'), '/').'/blog')
        );
});

test('blog post renders markdown headings and safe content', function () {
    $post = Post::factory()->published()->create([
        'title' => 'Build a blog',
        'slug' => 'build-a-blog',
        'content' => "## Step one\n\nUse **Markdown**.\n\n<script>alert('xss')</script>",
    ]);

    $this->get(route('blog.show', $post->slug))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Blog/Show')
            ->where('post.title', 'Build a blog')
            ->where('post.html', fn (string $html): bool => str_contains($html, '<h2>Step one</h2>')
                && str_contains($html, '<strong>Markdown</strong>')
                && ! str_contains($html, '<script>'))
            ->where('seo.canonical', rtrim((string) config('app.url'), '/').'/blog/build-a-blog')
        );
});

test('draft posts are not publicly accessible', function () {
    $post = Post::factory()->create();

    $this->get(route('blog.show', $post->slug))->assertNotFound();
});

test('the sitemap includes the blog index and published posts', function () {
    $post = Post::factory()->published()->create(['slug' => 'published-post']);

    $this->get(route('sitemap'))
        ->assertOk()
        ->assertSee('<loc>'.rtrim((string) config('app.url'), '/').'/blog</loc>', false)
        ->assertSee('<loc>'.rtrim((string) config('app.url'), '/').'/blog/'.$post->slug.'</loc>', false);
});

test('blog header copy is editable from portfolio content', function () {
    $content = config('portfolio.defaults');
    $content['blog_header']['title'] = 'Field notes';
    $content['blog_header']['description'] = 'A custom blog introduction.';

    PortfolioSetting::updateContent($content);

    $this->get(route('blog.index'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->where('portfolio.blog_header.title', 'Field notes')
            ->where('portfolio.blog_header.description', 'A custom blog introduction.')
        );
});
