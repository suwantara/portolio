<?php

namespace App\Providers;

use App\Http\Middleware\SecurityHeaders;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;
use Inertia\ExceptionResponse;
use Inertia\Inertia;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureDefaults();

        URL::forceRootUrl(rtrim((string) config('app.url'), '/'));

        Gate::define('manage-portfolio', fn (User $user): bool => (bool) $user->is_admin);
        Gate::define('manage-blog', fn (User $user): bool => (bool) $user->is_admin);

        Inertia::handleExceptionsUsing(function (ExceptionResponse $response) {
            $status = $response->statusCode();
            $request = $response->request;

            if (
                ($request->expectsJson() && ! $request->header('X-Inertia'))
                || ($status === 500 && config('app.debug'))
                || ! in_array($status, [401, 403, 404, 419, 429, 500, 503], true)
            ) {
                return SecurityHeaders::apply($response->response, $request);
            }

            return SecurityHeaders::apply(
                $response
                    ->render('ErrorPage', ['status' => $status])
                    ->withSharedData()
                    ->toResponse($request),
                $request,
            );
        });
    }

    /**
     * Configure default behaviors for production-ready applications.
     */
    protected function configureDefaults(): void
    {
        Date::use(CarbonImmutable::class);

        DB::prohibitDestructiveCommands(
            app()->isProduction(),
        );

        Password::defaults(fn (): ?Password => app()->isProduction()
            ? Password::min(12)
                ->mixedCase()
                ->letters()
                ->numbers()
                ->symbols()
                ->uncompromised()
            : null,
        );
    }
}
