<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Fortify;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Laravel\Fortify\Http\Requests\LoginRequest as FortifyLoginRequest;
use App\Http\Requests\LoginRequest as AppLoginRequest;

class FortifyServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(FortifyLoginRequest::class, AppLoginRequest::class);
    }

    public function boot(): void
    {
        $this->app->singleton(CreatesNewUsers::class, CreateNewUser::class);

        Fortify::registerView(fn () => view('auth.register'));
        Fortify::loginView(fn () => view('auth.login'));

        Fortify::authenticateUsing(function (Request $request) {
            if (Auth::attempt(
                $request->only('email', 'password'),
                $request->boolean('remember')
            )) {
                return Auth::user();
            }

            throw ValidationException::withMessages([
                'email' => ['メールアドレスまたはパスワードが一致しません'],
            ]);
        });

        RateLimiter::for('login', function (Request $request) {
            $email = (string) $request->email;

            return Limit::perMinute(10)->by($email . $request->ip());
        });

        Fortify::verifyEmailView(function () {
            return view('auth.verify-email');
        });
    }
}
