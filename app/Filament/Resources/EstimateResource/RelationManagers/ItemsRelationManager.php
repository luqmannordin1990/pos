<?php

namespace App\Filament\Resources\EstimateResource\RelationManagers;

use Filament\Forms;
use App\Models\Item;
use Filament\Tables;
use App\Models\Estimate;
use Filament\Forms\Form;
use App\Models\Directory;
use Filament\Tables\Table;
use Illuminate\Support\Arr;
use Livewire\Attributes\On;
use Filament\Facades\Filament;
use App\Events\ItemChangedEvent;
use Illuminate\Support\Facades\Event;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Support\Services\RelationshipJoiner;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Database\Eloquent\Relations\Relation;
use Filament\Resources\RelationManagers\RelationManager;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;


class ItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'items';
    public $totalrow;



    protected static string $view = 'filament.resources.EstimateResource.relation-manager';

    public function mount(): void
    {
        $this->loadDefaultActiveTab();
        $this->calculate();
    }

    public function form(Form $form): Form
    {
        return $form
        ->schema([
            \Filament\Forms\Components\Section::make()
                // ->description('Prevent abuse by limiting the number of requests per period')
                ->schema([
                    Forms\Components\TextInput::make('name')
                        ->required()
                        ->maxLength(255),

                    Forms\Components\RichEditor::make('info')
                        ->columnSpan('full'),

                    Forms\Components\Textarea::make('short_description')
                        ->rows(2)
                        ->columnSpan('full'),

                    Forms\Components\Select::make('categories')
                        ->multiple()
                        ->preload()
                        ->relationship(
                            name: 'categories',
                            titleAttribute: 'name',
                            modifyQueryUsing: fn(Builder $query) => $query->whereBelongsTo(Filament::getTenant())
                        )
                        ->createOptionForm([
                            Forms\Components\TextInput::make('name')
                                ->required(),
                            Forms\Components\Hidden::make('team_id')
                                ->default(Filament::getTenant()->id),
                        ]),

                    Forms\Components\TextInput::make('price')
                        ->numeric()
                        ->prefix(' RM')
                        ->required()
                        ->visible(fn($get) => $get('activate_product_variations') == false),

                    Forms\Components\TextInput::make('cost_price')
                        ->numeric()
                        ->prefix(' RM')
                        ->required()
                        ->visible(fn($get) => $get('activate_product_variations') == false),

                    Forms\Components\TextInput::make('weight')
                        ->numeric()
                        ->suffix(' kg')
                        ->required()
                        ->visible(fn($get) => $get('activate_product_variations') == false),

                    Forms\Components\TextInput::make('order_limit')
                        ->numeric()
                        ->nullable(),

                    Forms\Components\TextInput::make('current_stock_balance')
                        ->numeric()
                        ->default(0),

                    Forms\Components\Toggle::make('activate_ecommerce')
                        ->label('Activate in E-Commerce?')
                        ->default(true),

                    Forms\Components\Toggle::make('activate_stock_management')
                        ->label('Activate Stock Management?')
                        ->default(false),

                    Forms\Components\Toggle::make('activate_product_variations')
                        ->label('Activate Product Variations?')
                        ->live()
                        ->default(false),

                    Forms\Components\Select::make('directory')
                        ->options(function () {
                            return Directory::with('subcategories')->get()->mapWithKeys(function ($directory) {
                                return [
                                    $directory->name => $directory->subcategories->pluck('name', 'id')->toArray(),
                                ];
                            })->toArray();
                        })
                        ->searchable()
                        ->preload()
                        ->required(),

                    Forms\Components\Repeater::make('variations')
                        ->relationship()
                        ->schema([
                            Forms\Components\TextInput::make('name')->required(),
                            Forms\Components\TextInput::make('cost_price')
                                ->numeric()
                                ->required(),
                            Forms\Components\TextInput::make('price')
                                ->numeric()
                                ->required(),



                            Forms\Components\TextInput::make('weight')
                                ->numeric()
                                ->suffix(' kg')
                                ->required(),

                            Forms\Components\TextInput::make('order_limit')
                                ->numeric()
                                ->nullable(),

                            Forms\Components\TextInput::make('current_stock_balance')
                                ->numeric()
                                ->default(0),

                        ])
                        ->visible(fn($get) => $get('activate_product_variations') == true)
                        ->columns(6)
                        ->columnSpanFull(),


                    Forms\Components\FileUpload::make('product_image')
                        ->multiple()
                        ->downloadable()
                        ->directory('product_image')
                        ->acceptedFileTypes(['image/*'])
                        ->columnSpanFull(),
                    Forms\Components\FileUpload::make('attachment')
                        ->multiple()
                        ->downloadable()
                        ->directory('products')
                        ->acceptedFileTypes([
                            'image/*',          // Accept all image formats (jpg, png, gif, webp, etc.)
                            'application/pdf',  // PDF documents
                            'application/msword', // DOC
                            'application/vnd.openxmlformats-officedocument.*', // DOCX, XLSX, PPTX
                            'text/*',           // Plain text, CSV, JSON, XML, HTML, CSS
                            'audio/*',          // All audio formats (mp3, wav, ogg, etc.)
                            'video/*',          // All video formats (mp4, webm, mkv, etc.)
                            'application/zip',  // ZIP files
                            'application/x-rar-compressed', // RAR files
                        ])
                        ->columnSpanFull(),
                ])
                ->columns(2)
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('price')
                    ->label('Price')
                    ->state(fn($record) => $record->price),
             
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
                //
            ])
            ->headerActions([
                // Tables\Actions\CreateAction::make(),
                Tables\Actions\AttachAction::make()
                    ->name('Add Item')
                    ->icon('heroicon-m-plus-circle')
                    ->color('primary')
                    ->recordSelectOptionsQuery(function ($query) {
                        // Filter posts by the current tenant
                        return $query->whereBelongsTo(Filament::getTenant(), 'team');
                    })
                    ->preloadRecordSelect()
                    ->action(function (array $arguments, array $data, Form $form, Table $table): void {
                        $estimate = $this->ownerRecord->items()->syncWithoutDetaching([$data['recordId']]);
                        $this->calculate();
                    }),
            ])
            ->actions([
                // Tables\Actions\EditAction::make(),
                Tables\Actions\DetachAction::make()
                    ->action(function ($record): void {
                        $estimate = $this->ownerRecord->items()->detach([$record->item_id]);
                        $this->calculate();
                    }),
                // Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    // Tables\Actions\DetachBulkAction::make(),
                    // Tables\Actions\DeleteBulkAction::make(),

                ]),
            ]);
    }


    public function calculate()
    {
        $this->totalrow = $this->ownerRecord->items()->sum('price');
    }
}
