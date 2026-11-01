<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Http\Requests\BlogMediaRequest;
use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Models\Post;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class BlogController extends Controller
{
    /**
     * Display the blog management index.
     */
    public function index(): Response
    {
        $posts = Post::query()
            ->latest()
            ->select([
                'id',
                'title',
                'slug',
                'category',
                'status',
                'published_at',
                'reading_minutes',
                'updated_at',
            ])
            ->paginate(12)
            ->withQueryString();

        return Inertia::render('settings/Blog/Index', [
            'posts' => $posts,
        ]);
    }

    /**
     * Show the form for creating a post.
     */
    public function create(): Response
    {
        return Inertia::render('settings/Blog/Editor', [
            'post' => null,
            'action' => route('blog.store'),
            'method' => 'post',
            'media_url' => route('blog.media.store'),
            'preview_url' => route('blog.preview'),
            'categories' => Post::CATEGORIES,
        ]);
    }

    /**
     * Store a newly created post.
     */
    public function store(StorePostRequest $request): RedirectResponse
    {
        $post = Post::query()->create($this->payload($request->validated()));

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => __('Post created.'),
        ]);

        return to_route('blog.edit', $post);
    }

    /**
     * Show the form for editing a post.
     */
    public function edit(Post $post): Response
    {
        return Inertia::render('settings/Blog/Editor', [
            'post' => $post,
            'action' => route('blog.update', $post),
            'method' => 'put',
            'media_url' => route('blog.media.store'),
            'preview_url' => route('blog.preview'),
            'categories' => Post::CATEGORIES,
        ]);
    }

    /**
     * Update the specified post.
     */
    public function update(UpdatePostRequest $request, Post $post): RedirectResponse
    {
        $post->update($this->payload($request->validated(), $post));

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => __('Post updated.'),
        ]);

        return to_route('blog.edit', $post);
    }

    /**
     * Delete the specified post.
     */
    public function destroy(Post $post): RedirectResponse
    {
        $post->delete();

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => __('Post deleted.'),
        ]);

        return to_route('blog.manage');
    }

    /**
     * Upload an image for use in a post.
     */
    public function storeMedia(BlogMediaRequest $request): JsonResponse
    {
        $file = $request->file('file');

        abort_unless($file instanceof UploadedFile, 422);

        $path = $file->store('blog/images', 'public');

        abort_if($path === false, 500, 'Unable to store the uploaded image.');

        return response()->json([
            'url' => Storage::disk('public')->url($path),
            'path' => $path,
            'name' => $file->getClientOriginalName(),
        ]);
    }

    /**
     * Render a Markdown preview without saving the post.
     */
    public function preview(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'content' => ['required', 'string', 'max:200000'],
        ]);

        return response()->json([
            'html' => Post::renderMarkdown($validated['content']),
        ]);
    }

    /**
     * Build the persisted post payload.
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function payload(array $data, ?Post $post = null): array
    {
        $slugSource = (string) ($data['slug'] ?? '');

        if ($slugSource === '' && $post instanceof Post) {
            $slugSource = $post->slug;
        }

        if ($slugSource === '') {
            $slugSource = (string) $data['title'];
        }

        $status = (string) $data['status'];
        $publishedAt = null;

        if ($status === 'published') {
            $publishedAt = $post instanceof Post
                ? ($post->published_at ?? now())
                : now();
        }

        return [
            'title' => $data['title'],
            'slug' => $this->uniqueSlug($slugSource, $post),
            'category' => $data['category'],
            'excerpt' => $data['excerpt'] ?? null,
            'cover_image' => $data['cover_image'] ?? null,
            'content' => $data['content'],
            'status' => $status,
            'published_at' => $publishedAt,
            'reading_minutes' => Post::estimateReadingMinutes((string) $data['content']),
        ];
    }

    /**
     * Generate a unique slug from the given value.
     */
    private function uniqueSlug(string $value, ?Post $post = null): string
    {
        $base = Str::limit(Str::slug($value), 160, '');
        $base = $base !== '' ? $base : 'post-'.Str::lower(Str::random(8));
        $slug = $base;
        $suffix = 2;

        while (
            Post::query()
                ->where('slug', $slug)
                ->when($post instanceof Post, fn ($query) => $query->whereKeyNot($post->getKey()))
                ->exists()
        ) {
            $slug = $base.'-'.$suffix;
            $suffix++;
        }

        return $slug;
    }
}
