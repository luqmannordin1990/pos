<?php

namespace App\Filament\Pages;

use App\Models\Invoice;
use Livewire\Component;

use Filament\Pages\Page;
use Filament\Tables\Table;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\View\View;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Contracts\HasTable;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Tables\Concerns\InteractsWithTable;

class PageContoh extends Page implements HasForms, HasTable
{
    use InteractsWithTable;
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationLabel = 'Page Contoh';
    protected static ?string $slug = 'page-contoh/{pass?}';
    //layout ada index,simple,base
    protected static string $layout = 'filament-panels::components.layout.index';
    protected static string $view = 'filament.pages.page-contoh';
    protected static bool $shouldRegisterNavigation = false;
    public $pass;

    public function mount($pass = null)
    {
        $this->pass = 'world ' . $pass;
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

    public function table(Table $table): Table
    {
        $sub = DB::table('invoices')
        ->select('*');
        $sub = DB::table('users')->select('*');
    
     
        return $table
            ->query(Invoice::query()->fromSub($sub, 'invoices'))
            ->columns([
                TextColumn::make('id'),
            ])
            ->filters([
                // ...
            ])
            ->actions([
                // ...
            ])
            ->bulkActions([
                // ...
            ]);
    }
}
