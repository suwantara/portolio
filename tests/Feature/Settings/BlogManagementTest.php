<?php

use App\Models\Post;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Inertia\Testing\AssertableInertia as Assert;

test('guests cannot access blog management', function () {
    $this->get(route('blog.manage'))->assertRedirect(route('login'));
});

test('non admin users cannot access blog management', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('blog.manage'))
        ->assertForbidden();

    $this->actingAs($user)
        ->post(route('blog.store'), [
            'title' => 'Nope',
            'category' => 'tutorial',
            'content' => '# Nope',
            'status' => 'draft',
        ])
        ->assertForbidden();
});

test('admins can open the blog manager and editor', function () {
    $user = User::factory()->admin()->create();
    $post = Post::factory()->create();

    $this->actingAs($user)
        ->get(route('blog.manage'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('settings/Blog/Index')
            ->has('posts.data', 1)
        );

    $this->actingAs($user)
        ->get(route('blog.create'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('settings/Blog/Editor')
            ->where('post', null)
        );

    $this->actingAs($user)
        ->get(route('blog.edit', $post))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('settings/Blog/Editor')
            ->where('post.slug', $post->slug)
        );
});

test('admins can create and publish a post', function () {
    $user = User::factory()->admin()->create();

    $this->actingAs($user)
        ->post(route('blog.store'), [
            'title' => 'My first tutorial',
            'slug' => '',
            'category' => 'tutorial',
            'excerpt' => 'A short overview.',
            'cover_image' => null,
            'content' => "## Start here\n\nBuild something useful.",
            'status' => 'published',
        ])
        ->assertSessionHasNoErrors()
        ->assertRedirect();

    $post = Post::query()->firstOrFail();

    expect($post->slug)->toBe('my-first-tutorial');
    expect($post->status)->toBe('published');
    expect($post->published_at)->not->toBeNull();
    expect($post->reading_minutes)->toBeGreaterThanOrEqual(1);
});

test('admins can update a post and move it back to draft', function () {
    $user = User::factory()->admin()->create();
    $post = Post::factory()->published()->create();

    $this->actingAs($user)
        ->put(route('blog.update', $post), [
            'title' => 'Updated title',
            'slug' => $post->slug,
            'category' => 'tips',
            'excerpt' => 'Updated excerpt.',
            'cover_image' => null,
            'content' => 'Updated content.',
            'status' => 'draft',
        ])
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('blog.edit', $post));

    $post->refresh();

    expect($post->title)->toBe('Updated title');
    expect($post->category)->toBe('tips');
    expect($post->status)->toBe('draft');
    expect($post->published_at)->toBeNull();
});

test('admins can preview markdown and raw html is stripped', function () {
    $user = User::factory()->admin()->create();

    $this->actingAs($user)
        ->postJson(route('blog.preview'), [
            'content' => "## Preview\n\n<script>alert('xss')</script>",
        ])
        ->assertOk()
        ->assertJsonPath(
            'html',
            fn (string $html): bool => str_contains($html, '<h2>Preview</h2>')
                && ! str_contains($html, '<script>'),
        );
});

test('admins can upload blog images and unsafe files are rejected', function () {
    Storage::fake('public');

    $user = User::factory()->admin()->create();

    $response = $this->actingAs($user)
        ->postJson(route('blog.media.store'), [
            'file' => UploadedFile::fake()->image('diagram.png', 800, 600),
        ])
        ->assertOk()
        ->assertJsonStructure(['url', 'path', 'name']);

    $path = $response->json('path');

    expect($path)->toStartWith('blog/images/');
    Storage::disk('public')->assertExists($path);

    $this->actingAs($user)
        ->postJson(route('blog.media.store'), [
            'file' => UploadedFile::fake()->create('diagram.svg', 10, 'image/svg+xml'),
        ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors('file');
});

test('admins can delete a post', function () {
    $user = User::factory()->admin()->create();
    $post = Post::factory()->create();

    $this->actingAs($user)
        ->delete(route('blog.destroy', $post))
        ->assertRedirect(route('blog.manage'));

    expect(Post::query()->exists())->toBeFalse();
});
