<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\TokenService;
use App\Services\JwtTokenService;
use App\Services\TokenServiceInterface;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(TokenServiceInterface::class, function ($app) {
            $authStyle = config('auth.token_style');
            if ($authStyle === 'jwt') {
                return new JwtTokenService();
            }

            return new TokenService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        RateLimiter::for('login', function (Request $request) {
            return Limit::perMinute(5);
        });

        RateLimiter::for('password-reset-otp', function (Request $request) {
            return Limit::perMinutes(3, 15);
        });
    }
}
