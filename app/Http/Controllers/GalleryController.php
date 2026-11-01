<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\BuildsPortfolioSeo;
use App\Models\PortfolioSetting;
use Inertia\Inertia;
use Inertia\Response;

class GalleryController extends Controller
{
    use BuildsPortfolioSeo;

    /**
     * Display the full portfolio gallery.
     */
    public function __invoke(): Response
    {
        $portfolio = PortfolioSetting::content();

        return Inertia::render('Gallery', [
            'portfolio' => $portfolio,
            'seo' => $this->gallerySeo($portfolio),
        ]);
    }
}
