<?php

use App\Http\Controllers\Settings\BlogController as BlogManagementController;
use App\Http\Controllers\Settings\PortfolioController;
use App\Http\Controllers\Settings\ProfileController;
use App\Http\Controllers\Settings\SecurityController;
use App\Http\Controllers\Teams\TeamController;
use App\Http\Controllers\Teams\TeamInvitationController;
use App\Http\Controllers\Teams\TeamMemberController;
use App\Http\Middleware\EnsureTeamMembership;
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
        Route::post('settings/portfolio/media', [PortfolioController::class, 'storeMedia'])->name('portfolio.media.store');
        Route::delete('settings/portfolio/media', [PortfolioController::class, 'destroyMedia'])->name('portfolio.media.destroy');
    });

    Route::middleware('can:manage-blog')->group(function () {
        Route::get('settings/blog', [BlogManagementController::class, 'index'])->name('blog.manage');
        Route::get('settings/blog/create', [BlogManagementController::class, 'create'])->name('blog.create');
        Route::post('settings/blog', [BlogManagementController::class, 'store'])->name('blog.store');
        Route::post('settings/blog/preview', [BlogManagementController::class, 'preview'])->name('blog.preview');
        Route::post('settings/blog/media', [BlogManagementController::class, 'storeMedia'])->name('blog.media.store');
        Route::get('settings/blog/{post:slug}/edit', [BlogManagementController::class, 'edit'])->name('blog.edit');
        Route::put('settings/blog/{post:slug}', [BlogManagementController::class, 'update'])->name('blog.update');
        Route::delete('settings/blog/{post:slug}', [BlogManagementController::class, 'destroy'])->name('blog.destroy');
    });

    Route::get('settings/teams', [TeamController::class, 'index'])->name('teams.index');
    Route::post('settings/teams', [TeamController::class, 'store'])->name('teams.store');

    Route::middleware(EnsureTeamMembership::class)->group(function () {
        Route::get('settings/teams/{team}', [TeamController::class, 'edit'])->name('teams.edit');
        Route::patch('settings/teams/{team}', [TeamController::class, 'update'])->name('teams.update');
        Route::delete('settings/teams/{team}', [TeamController::class, 'destroy'])->name('teams.destroy');
        Route::post('settings/teams/{team}/switch', [TeamController::class, 'switch'])->name('teams.switch');
        Route::delete('settings/teams/{team}/leave', [TeamController::class, 'leave'])->name('teams.leave');

        Route::patch('settings/teams/{team}/members/{user}', [TeamMemberController::class, 'update'])->name('teams.members.update');
        Route::delete('settings/teams/{team}/members/{user}', [TeamMemberController::class, 'destroy'])->name('teams.members.destroy');

        Route::post('settings/teams/{team}/invitations', [TeamInvitationController::class, 'store'])->name('teams.invitations.store');
        Route::delete('settings/teams/{team}/invitations/{invitation}', [TeamInvitationController::class, 'destroy'])->name('teams.invitations.destroy');
    });
});
