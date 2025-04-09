<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\Item;
use Filament\Tables;
use App\Models\Estimate;
use Filament\Forms\Form;
use Filament\Tables\Table;

use Filament\Facades\Filament;
use Filament\Resources\Resource;
use Awcodes\TableRepeater\Header;
use Illuminate\Support\HtmlString;
use Filament\Support\Enums\Alignment;
use Illuminate\Support\Facades\Blade;
use Filament\Support\Enums\ActionSize;
use Filament\Forms\Components\Repeater;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\Actions\Action;
use App\Filament\Resources\EstimateResource\Pages;
use Awcodes\TableRepeater\Components\TableRepeater;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\EstimateResource\RelationManagers;

class EstimateResource extends Resource
{
    protected static ?string $model = Estimate::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {

        return $form
            ->schema([
                \Filament\Forms\Components\Section::make('')
                    ->id('estimate')
                    ->schema([

                        Forms\Components\Select::make('customer_id')
                            ->relationship(
                                name: 'customer',
                                titleAttribute: 'name',
                                modifyQueryUsing: fn(Builder $query) => $query->whereBelongsTo(Filament::getTenant())
                            )
                            ->required()
                            ->searchable()
                            ->preload(),
                        Forms\Components\DatePicker::make('date')
                            ->default(date('Y-m-d'))
                            ->required(),
                        Forms\Components\DatePicker::make('expiry_date')
                            ->default(date('Y-m-d'))
                            ->required(),
                        Forms\Components\TextInput::make('estimate_number')
                            ->readonly()
                            ->visible(fn($operation) => $operation == 'edit'),
                        Forms\Components\Textarea::make('notes')
                            ->columnSpanFull(),

                        TableRepeater::make('item_estimate')
                            ->live()
                            ->headers([
                                Header::make('name')->width('150px'),
                                Header::make('price')->width('150px'),
                                Header::make('total')->width('150px'),
                            ])
                            ->schema([
                                \Filament\Forms\Components\Select::make('id')
                                    ->options(Item::where('team_id', Filament::getTenant()->id)
                                        ->pluck('name', 'id'))
                                    ->required()
                                    ->afterStateUpdated(fn($set, $state) => $set('price', Item::find($state)->price)),
                                \Filament\Forms\Components\TextInput::make('price')
                                    ->readonly()
                                    ->required(),
                                \Filament\Forms\Components\TextInput::make('pivot.total')
                                    ->required(),
                            ])
                            ->addActionAlignment(Alignment::Start)
                            ->columnSpan('full'),
                        \Filament\Forms\Components\Placeholder::make('footer')
                            ->hiddenLabel()
                            ->content(fn($record, $get) => dd($get('item_estimate')))
                            ->content(function ($record, $get) {
                                $html = '';
                                $total_price = 0;
                                foreach ($get('item_estimate') as $key => $value) {
                                    $total_price += $value['pivot']['total'] * $value['price'];
                                }
                                $html .= Blade::render('<div>Total: ' . $total_price . '</div>');
                                return new HtmlString($html);
                            })
                        // ->content(fn($record, $get) => new HtmlString(Blade::render('<div>Hello, {{ auth()->user()->name }}!</div>')))
                    ])
                    ->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('customer.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('expiry_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('estimate_number')
                    ->searchable(),
                Tables\Columns\TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ])
            ->recordUrl(null)
            ->recordAction(null)
            ->defaultSort('updated_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            //
            // RelationManagers\ItemsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEstimates::route('/'),
            'create' => Pages\CreateEstimate::route('/create'),
            'edit' => Pages\EditEstimate::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
