<?php

namespace App\Providers;

use Livewire\Livewire;
use App\Policies\UserPolicy;
use Filament\View\PanelsRenderHook;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Route;
use App\Http\Responses\LogoutResponse;
use App\Policies\ActivityLoggerPolicy;
use Illuminate\Support\ServiceProvider;
use Spatie\Activitylog\Models\Activity;
use Filament\Support\Facades\FilamentView;
use App\Http\Responses\Auth\CustomPasswordResetResponse;
use Filament\Http\Responses\Auth\Contracts\LoginResponse;
use Filament\Http\Responses\Auth\Contracts\PasswordResetResponse;
use Filament\Http\Responses\Auth\Contracts\LogoutResponse as LogoutResponseContract;
 

class AppServiceProvider extends ServiceProvider
{

    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(
            LoginResponse::class,
            \App\Http\Responses\Auth\LoginResponse::class
        );
        $this->app->bind(LogoutResponseContract::class, LogoutResponse::class);
        $this->app->bind(PasswordResetResponse::class, CustomPasswordResetResponse::class);
        //
        FilamentView::registerRenderHook(
            PanelsRenderHook::BODY_END,
            // fn () => view('customFooter'),
            fn () => Blade::render('@livewire(\'footer\')')
        );

        FilamentView::registerRenderHook(
            PanelsRenderHook::BODY_END,
            // fn () => view('customFooter'),
            fn () => Blade::render('@livewire(\'preloader\')')
        );

    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
        if($this->app->environment('production')) {
            // URL::forceScheme('https');
            $this->app['request']->server->set('HTTPS', true);
        }

        Livewire::setScriptRoute(function ($handle) {
            return Route::get('/livewire/livewire.js', $handle)->middleware('web');
        });
        Livewire::setUpdateRoute(function ($handle) {
            return Route::post('/livewire/update', $handle)->middleware('web')->name('custom-update');
        });


        // Gate::policy(Activity::class, ActivityLoggerPolicy::class);
       
    }
}
