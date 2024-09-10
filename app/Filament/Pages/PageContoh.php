<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Illuminate\Contracts\View\View;

class PageContoh extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationLabel = 'Page Contoh';
    protected static ?string $slug = 'page-contoh/{pass?}';
    //layout ada index,simple,base
    protected static string $layout = 'filament-panels::components.layout.index';
    protected static string $view = 'filament.pages.page-contoh';
    public $pass ;

    public function mount($pass = null){
        $this->pass = 'world '.$pass;
    }

    public function render(): View
    {
        return view($this->getView(), $this->getViewData())
            ->layout($this->getLayout(), [
                'livewire' => $this,
                'maxContentWidth' => $this->getMaxContentWidth(),
                ...$this->getLayoutData(),
            ])
            ->with([
                'passUserData' => auth()->user()->name,
            ]);
    }
}
