<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\BuildsPortfolioSeo;
use App\Models\PortfolioSetting;
use App\Support\ProjectContent;
use Inertia\Inertia;
use Inertia\Response;

class WorkController extends Controller
{
    use BuildsPortfolioSeo;

    /**
     * Display the full project list.
     */
    public function __invoke(): Response
    {
        $portfolio = PortfolioSetting::content();

        return Inertia::render('Work/Index', [
            'portfolio' => $portfolio,
            'seo' => $this->workSeo($portfolio),
        ]);
    }

    /**
     * Display a single project case study.
     */
    public function show(string $slug): Response
    {
        $portfolio = PortfolioSetting::content();
        $projects = $portfolio['projects'] ?? null;
        $project = is_array($projects)
            ? ProjectContent::find($projects, $slug)
            : null;

        abort_if($project === null, 404);

        return Inertia::render('Work/Show', [
            'portfolio' => $portfolio,
            'project' => $project,
            'seo' => $this->projectSeo($portfolio, $project),
        ]);
    }
}
