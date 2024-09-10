<?php

namespace App\Filament\Admin\Themes;

use Filament\Panel;
use Filament\Support\Colors\Color;
use Hasnayeen\Themes\Contracts\Theme;
use Filament\FontProviders\GoogleFontProvider;
use Hasnayeen\Themes\Contracts\HasOnlyLightMode;
use Hasnayeen\Themes\Contracts\HasChangeableColor;
use Hasnayeen\Themes\Contracts\CanModifyPanelConfig;

class Lppsa implements CanModifyPanelConfig, HasOnlyLightMode, Theme, HasChangeableColor
{
    public static function getName(): string
    {
        return 'lppsa';
    }

    public static function getPath(): string
    {
        return 'resources/css/filament/admin/themes/lppsa.css';
    }

    public function getThemeColor(): array
    {
        return Color::all();
    }

    public function getPrimaryColor(): array
    {
        return ['primary' => $this->getThemeColor()['blue']];
    }

    public function modifyPanelConfig(Panel $panel): Panel
    {
        return $panel
            ->viteTheme($this->getPath())
            ->font('Roboto', provider: GoogleFontProvider::class);
    }

}
