<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\BuildsPortfolioSeo;
use App\Models\PortfolioSetting;
use Inertia\Inertia;
use Inertia\Response;

class PortfolioController extends Controller
{
    use BuildsPortfolioSeo;

    /**
     * Display the public portfolio landing page.
     */
    public function __invoke(): Response
    {
        $portfolio = PortfolioSetting::content();

        return Inertia::render('Welcome', [
            'portfolio' => $portfolio,
            'seo' => $this->homeSeo($portfolio),
        ]);
    }
}
