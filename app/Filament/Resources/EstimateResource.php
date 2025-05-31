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
                                Header::make('quantity')->width('150px'),
                                Header::make('price')->width('150px'),
                                Header::make('amount')->width('150px'),
                            ])
                            ->schema([
                                \Filament\Forms\Components\Select::make('id')
                                    ->options(Item::where('team_id', Filament::getTenant()->id)
                                        ->pluck('name', 'id'))
                                    ->searchable()
                                    ->required()
                                    ->afterStateUpdated(function($set, $get, $state){
                                        self::calculate_total($set, $get); 
                                    }),
                                \Filament\Forms\Components\TextInput::make('pivot.quantity')
                                    ->integer()  // The input should be an integer.
                                    ->required()
                                    ->afterStateUpdated(function($set, $get, $state){
                                        self::calculate_total($set, $get);
                                    
                                    }),
                                \Filament\Forms\Components\TextInput::make('price')
                                    ->readonly()
                                    ->dehydrated(false)
                                    ->required(),
                                \Filament\Forms\Components\TextInput::make('amount')
                                    ->readonly()
                                    ->dehydrated(false)
                                    ->required(),

                            ])
                            ->addActionAlignment(Alignment::Start)
                            ->columnSpan('full'),
                        \Filament\Forms\Components\Placeholder::make('footer')
                            ->hiddenLabel()
                            ->content(function ($record, $get) {
                                $html = '';
                                $sub_total = 0;
                                foreach ($get('item_estimate') as $key => $value) {
                                    $sub_total += (float)$value['pivot']['quantity'] * $value['price'];
                                }
                                $html .= Blade::render('
                                    <div class="flex flex-col justify-center items-end bg-gray-100 rounded">
                                            <div class="w-1/3  flex justify-between p-2">
                                                    <span class="font-bold dark:text-black">Sub Total</span>
                                                    <span class="font-bold dark:text-black">' . number_format($sub_total,'2','.','') . '</span>
                                            </div>
                                            <div class="w-1/3  flex justify-between p-2">
                                                    <span class="font-bold dark:text-black text-lg">Total</span>
                                                    <span class="font-bold dark:text-black text-lg">' . number_format($sub_total,'2','.','')  . '</span>
                                            </div>

                                    </div>

                                ');
                                return new HtmlString($html);
                            })
                            ->columnSpan('full'),
                        // ->content(fn($record, $get) => new HtmlString(Blade::render('<div>Hello, {{ auth()->user()->name }}!</div>')))
                    ])
                    ->columns(2)
            ]);
    }

    static function  calculate_total($set, $get){
        $price = Item::find($get('id'))?->price ?? 0;
        $amount = (float)$price * (float)$get('pivot.quantity');
        $set('price', $price);
        $set('amount', $amount);
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
                Tables\Actions\DeleteAction::make()
                    ->action(fn($record) => $record->delete()),
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
