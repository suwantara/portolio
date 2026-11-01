<?php

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

test('guests cannot upload portfolio media', function () {
    Storage::fake('public');

    $this->postJson(route('portfolio.media.store'), [
        'kind' => 'image',
        'file' => UploadedFile::fake()->image('avatar.png', 100, 100),
    ])->assertUnauthorized();
});

test('non admin users cannot upload portfolio media', function () {
    Storage::fake('public');

    $user = User::factory()->create();

    $this->actingAs($user)
        ->postJson(route('portfolio.media.store'), [
            'kind' => 'image',
            'file' => UploadedFile::fake()->image('avatar.png', 100, 100),
        ])
        ->assertForbidden();

    expect(Storage::disk('public')->allFiles())->toBeEmpty();
});

test('admins can upload images', function () {
    Storage::fake('public');

    $user = User::factory()->admin()->create();

    $response = $this->actingAs($user)
        ->postJson(route('portfolio.media.store'), [
            'kind' => 'image',
            'file' => UploadedFile::fake()->image('avatar.png', 400, 400),
        ])
        ->assertOk()
        ->assertJsonPath('kind', 'image')
        ->assertJsonPath('name', 'avatar.png')
        ->assertJsonStructure(['url', 'path', 'name', 'mime', 'size', 'kind']);

    $path = $response->json('path');

    expect($path)->toStartWith('portfolio/images/');
    Storage::disk('public')->assertExists($path);
});

test('admins can upload documents', function () {
    Storage::fake('public');

    $user = User::factory()->admin()->create();

    $response = $this->actingAs($user)
        ->postJson(route('portfolio.media.store'), [
            'kind' => 'document',
            'file' => UploadedFile::fake()->create('cv.pdf', 200, 'application/pdf'),
        ])
        ->assertOk()
        ->assertJsonPath('kind', 'document')
        ->assertJsonPath('name', 'cv.pdf');

    $path = $response->json('path');

    expect($path)->toStartWith('portfolio/documents/');
    Storage::disk('public')->assertExists($path);
});

test('unsupported files are rejected', function () {
    Storage::fake('public');

    $user = User::factory()->admin()->create();

    $this->actingAs($user)
        ->postJson(route('portfolio.media.store'), [
            'kind' => 'image',
            'file' => UploadedFile::fake()->create('script.svg', 10, 'image/svg+xml'),
        ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors('file');

    expect(Storage::disk('public')->allFiles())->toBeEmpty();
});

test('uploaded files can be deleted', function () {
    Storage::fake('public');

    $user = User::factory()->admin()->create();
    $path = 'portfolio/images/old.png';

    Storage::disk('public')->put($path, 'image-data');

    $this->actingAs($user)
        ->deleteJson(route('portfolio.media.destroy'), [
            'url' => Storage::disk('public')->url($path),
        ])
        ->assertNoContent();

    Storage::disk('public')->assertMissing($path);
});

test('files outside the portfolio directory cannot be deleted', function () {
    Storage::fake('public');

    $user = User::factory()->admin()->create();
    $path = 'avatars/private.png';

    Storage::disk('public')->put($path, 'private-data');

    $url = Storage::disk('public')->url($path);

    expect(Str::contains($url, '/storage/avatars/private.png'))->toBeTrue();

    $this->actingAs($user)
        ->deleteJson(route('portfolio.media.destroy'), ['url' => $url])
        ->assertNoContent();

    Storage::disk('public')->assertExists($path);
});
