<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Http\Requests\Settings\PortfolioMediaRequest;
use App\Http\Requests\Settings\PortfolioUpdateRequest;
use App\Models\PortfolioSetting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class PortfolioController extends Controller
{
    /**
     * The editable portfolio sections.
     *
     * @var array<int, string>
     */
    public const SECTIONS = [
        'profile',
        'about',
        'experience',
        'work',
        'skills',
        'education',
        'gallery',
        'documents',
        'pages',
        'cta',
    ];

    /**
     * Show the portfolio content editor.
     */
    public function edit(): Response
    {
        return Inertia::render('settings/Portfolio', [
            'portfolio' => PortfolioSetting::content(),
            'options' => config('portfolio.options'),
        ]);
    }

    /**
     * Update the portfolio content.
     */
    public function update(PortfolioUpdateRequest $request): RedirectResponse
    {
        PortfolioSetting::updateContent($request->validated());

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Portfolio content updated.')]);

        $section = $request->query('section');

        if (
            is_string($section)
            && $section !== 'profile'
            && in_array($section, self::SECTIONS, true)
        ) {
            return to_route('portfolio.section', $section);
        }

        return to_route('portfolio.edit');
    }

    /**
     * Upload an image or document used by the portfolio.
     */
    public function storeMedia(PortfolioMediaRequest $request): JsonResponse
    {
        $file = $request->file('file');

        abort_unless($file instanceof UploadedFile, 422);

        $kind = $request->validated('kind');
        $directory = $kind === 'image' ? 'portfolio/images' : 'portfolio/documents';

        $path = $file->store($directory, 'public');

        abort_if($path === false, 500, 'Unable to store the uploaded file.');

        return response()->json([
            'url' => Storage::disk('public')->url($path),
            'path' => $path,
            'name' => $file->getClientOriginalName(),
            'mime' => $file->getClientMimeType(),
            'size' => $file->getSize(),
            'kind' => $kind,
        ]);
    }

    /**
     * Delete a previously uploaded portfolio file.
     */
    public function destroyMedia(Request $request): HttpResponse
    {
        $validated = $request->validate([
            'url' => ['required', 'string', 'max:2048'],
        ]);

        $path = $this->storagePathFromUrl($validated['url']);

        if ($path !== null) {
            Storage::disk('public')->delete($path);
        }

        return response()->noContent();
    }

    /**
     * Extract a public disk path from a stored URL.
     */
    private function storagePathFromUrl(string $url): ?string
    {
        $path = parse_url($url, PHP_URL_PATH);

        if (! is_string($path)) {
            return null;
        }

        $marker = '/storage/';
        $position = strpos($path, $marker);

        if ($position === false) {
            return null;
        }

        $relative = substr($path, $position + strlen($marker));

        if (! str_starts_with($relative, 'portfolio/') || str_contains($relative, '..')) {
            return null;
        }

        return $relative;
    }
}
