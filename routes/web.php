<?php

use App\Http\Controllers\BlogController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GalleryController;
use App\Http\Controllers\LlmsTxtController;
use App\Http\Controllers\PortfolioController;
use App\Http\Controllers\RobotsController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\Teams\TeamInvitationController;
use App\Http\Middleware\EnsureTeamMembership;
use Illuminate\Support\Facades\Route;

Route::get('/', PortfolioController::class)->name('home');
Route::get('gallery', GalleryController::class)->name('gallery');
Route::get('blog', [BlogController::class, 'index'])->name('blog.index');
Route::get('blog/{slug}', [BlogController::class, 'show'])->name('blog.show');
Route::get('sitemap.xml', SitemapController::class)->name('sitemap');
Route::get('robots.txt', RobotsController::class)->name('robots');
Route::get('llms.txt', LlmsTxtController::class)->name('llms');

Route::prefix('{current_team}')
    ->middleware(['auth', EnsureTeamMembership::class])
    ->group(function () {
        Route::get('dashboard', DashboardController::class)->name('dashboard');
    });

Route::middleware(['auth'])->group(function () {
    Route::post('invitations/{invitation}/accept', [TeamInvitationController::class, 'accept'])->name('invitations.accept');
    Route::delete('invitations/{invitation}', [TeamInvitationController::class, 'decline'])->name('invitations.decline');
});

require __DIR__.'/settings.php';
