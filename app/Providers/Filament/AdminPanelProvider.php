<?php

namespace App\Providers\Filament;

use Filament\Pages;
use Filament\Panel;
use Filament\Widgets;
use Filament\PanelProvider;
use App\Filament\Auth\Login;
use Filament\Support\Assets\Js;
use Filament\Support\Assets\Css;
use App\Filament\Admin\Themes\Luq;
use Filament\Support\Colors\Color;
use Filament\View\PanelsRenderHook;
use App\Filament\Admin\Themes\Lppsa;
use Illuminate\Support\Facades\Blade;
use Filament\Navigation\NavigationItem;
use Filament\Navigation\NavigationGroup;
use App\Filament\Admin\Themes\Neumorphism;
use Filament\Http\Middleware\Authenticate;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login(Login::class)
            ->profile()
            ->spa()
            ->sidebarCollapsibleOnDesktop()
            ->brandLogo(asset('assets/logo.png'))
            ->brandLogoHeight('3rem')
            ->favicon(asset('assets/logo.png'))
            ->colors([
                'primary' => Color::Amber,
            ])
            ->viteTheme('resources/css/filament/admin/theme.css')
            ->assets([
                Css::make('custom-stylesheet', asset('css/custom.css')),
                Js::make('custom-script', asset('js/custom.js')),
            ])
            ->renderHook(
                PanelsRenderHook::AUTH_LOGIN_FORM_BEFORE,
                fn (): string => Blade::render('
                   <div class="flex justify-center font-bold">{{ config("app.name") }}</div>
                ')
            )
            ->navigationItems([])
            ->navigationGroups([
                NavigationGroup::make()
                     ->label('Maintenance')
                     ->icon('heroicon-o-wrench'),
            ])
            ->resources([
                config('filament-logger.activity_resource')
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
                // Widgets\FilamentInfoWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
                \Hasnayeen\Themes\Http\Middleware\SetTheme::class
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->plugin(
                \Hasnayeen\Themes\ThemesPlugin::make()
                ->registerTheme([
                    Lppsa::getName() => Lppsa::class,
                    Neumorphism::getName() => Neumorphism::class
                    ])
                    
            );
    }
}
