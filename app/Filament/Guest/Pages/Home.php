<?php

namespace App\Filament\Guest\Pages;

use Filament\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;

class Home extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.guest.pages.home';
    protected static ?string $slug = '/';


    public function getTitle(): string | Htmlable
    {
        return __('');
    }
}
