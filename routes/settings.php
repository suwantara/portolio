<?php

use App\Http\Controllers\Settings\BlogController as BlogManagementController;
use App\Http\Controllers\Settings\PortfolioController;
use App\Http\Controllers\Settings\ProfileController;
use App\Http\Controllers\Settings\SecurityController;
use Illuminate\Auth\Middleware\RequirePassword;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', '/settings/profile');

    Route::get('settings/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('settings/profile', [ProfileController::class, 'update'])->name('profile.update');
});

Route::middleware(['auth'])->group(function () {
    Route::delete('settings/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('settings/security', [SecurityController::class, 'edit'])
        ->middleware(RequirePassword::class)
        ->name('security.edit');

    Route::put('settings/password', [SecurityController::class, 'update'])
        ->middleware('throttle:6,1')
        ->name('user-password.update');

    Route::inertia('settings/appearance', 'settings/Appearance')->name('appearance.edit');

    Route::middleware('can:manage-portfolio')->group(function () {
        Route::get('settings/portfolio', [PortfolioController::class, 'edit'])->name('portfolio.edit');
        Route::put('settings/portfolio', [PortfolioController::class, 'update'])->name('portfolio.update');
        Route::get('settings/portfolio/{section}', [PortfolioController::class, 'edit'])
            ->whereIn('section', PortfolioController::SECTIONS)
            ->name('portfolio.section');
        Route::post('settings/portfolio/media', [PortfolioController::class, 'storeMedia'])->name('portfolio.media.store');
        Route::delete('settings/portfolio/media', [PortfolioController::class, 'destroyMedia'])->name('portfolio.media.destroy');
    });

    Route::middleware('can:manage-blog')->group(function () {
        Route::get('settings/portfolio/blog', [BlogManagementController::class, 'index'])->name('blog.manage');
        Route::get('settings/portfolio/blog/create', [BlogManagementController::class, 'create'])->name('blog.create');
        Route::post('settings/portfolio/blog', [BlogManagementController::class, 'store'])->name('blog.store');
        Route::post('settings/portfolio/blog/preview', [BlogManagementController::class, 'preview'])->name('blog.preview');
        Route::post('settings/portfolio/blog/media', [BlogManagementController::class, 'storeMedia'])->name('blog.media.store');
        Route::get('settings/portfolio/blog/{post:slug}/edit', [BlogManagementController::class, 'edit'])->name('blog.edit');
        Route::put('settings/portfolio/blog/{post:slug}', [BlogManagementController::class, 'update'])->name('blog.update');
        Route::delete('settings/portfolio/blog/{post:slug}', [BlogManagementController::class, 'destroy'])->name('blog.destroy');
    });
});
