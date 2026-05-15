<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Http\Requests\AdminLoginRequest;
use App\Http\Requests\LoginRequest;
use Laravel\Fortify\Contracts\LoginResponse;
use Laravel\Fortify\Contracts\RegisterResponse;
use Laravel\Fortify\Contracts\LogoutResponse;
use Laravel\Fortify\Http\Requests\LoginRequest as FortifyLoginRequest;
use Illuminate\Support\Facades\Auth;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(FortifyLoginRequest::class, AdminLoginRequest::class);

        $this->app->instance(LoginResponse::class, new class implements LoginResponse {
            public function toResponse($request)
            {
                if (auth()->user() instanceof \App\Models\Admin) {
                    return redirect('/admin/attendance/list')->with('flashSuccess', 'ログインしました');
                }
                return redirect('/attendance')->with('flashSuccess', 'ログインしました');
            }
        });

        $this->app->instance(RegisterResponse::class, new class implements RegisterResponse {
            public function toResponse($request)
            {
                return redirect('/attendance')->with('flashSuccess', '会員登録が完了しました');
            }
        });

        $this->app->instance(LogoutResponse::class, new class implements LogoutResponse {
            public function toResponse($request)
            {
                return redirect('/login')->with('flashSuccess', 'ログアウトしました');
            }
        });
    }


    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
