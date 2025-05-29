<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Customer;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Select;
use Filament\Support\Enums\ActionSize;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Exports\CustomerExporter;
use App\Filament\Imports\CustomerImporter;
use App\Filament\Resources\CustomerResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\CustomerResource\RelationManagers;
use App\Models\User;

class CustomerResource extends Resource
{
    protected static ?string $model = Customer::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?int $navigationSort = 2;
    // protected static ?string $navigationGroup = 'Database';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                \Filament\Forms\Components\Section::make()
                    // ->description('Prevent abuse by limiting the number of requests per period')
                    ->schema([
                        // ...
                        Select::make('user_id')
                            ->label('User')
                            ->options(function ($operation, $state) {

                                return User::whereHas('team', function ($query) {
                                    $query->where('team_id', filament()->getTenant()->id);
                                })
                                    ->where(function ($query) use ($operation, $state) {
                                        if ($operation == 'edit') {
                                            $query->whereNotIn('id', Customer::where('team_id', filament()->getTenant()->id)->pluck('user_id'))
                                                ->orWhere('id', $state);
                                        } else {
                                            $query->whereNotIn('id', Customer::where('team_id', filament()->getTenant()->id)->pluck('user_id'));
                                        }
                                    })
                                    ->pluck('name', 'id');
                            })
                            ->live()
                            ->afterStateUpdated(function ($set, $state) {
                                $user = User::find($state);
                                $set('name', $user?->name);
                                $set('email', $user?->email);
                                $set('telephone_no', $user?->phone);
                            })
                            ->searchable()
                            ->preload(),

                        TextInput::make('name')
                            ->label('Customer Name')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('house_unit_no')
                            ->label('House/Unit No.')
                            ->maxLength(255),

                        TextInput::make('telephone_no')
                            ->label('Telephone No.')
                            ->tel() // Enforces a telephone number format
                            ->nullable()
                            ->maxLength(20),

                        Textarea::make('address')
                            ->label('Address')
                            ->nullable()
                            ->columnSpanFull(),

                        TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->unique('customers', 'email', ignoreRecord: true)
                            ->required()
                            ->maxLength(255),

                        TextInput::make('city_district')
                            ->label('City/District')
                            ->nullable()
                            ->maxLength(255),

                        TextInput::make('ic_mykad')
                            ->label('IC/MyKad')
                            ->unique('customers', 'ic_mykad', ignoreRecord: true)
                            ->required()
                            ->maxLength(255),

                        TextInput::make('postal_code')
                            ->label('Postal Code')
                            ->nullable()
                            ->maxLength(10),

                        DatePicker::make('date_of_birth')
                            ->label('Date of Birth')
                            ->displayFormat('d/m/Y')
                            ->nullable()
                            ->native(false) // Uses Filament's date picker UI
                            ->closeOnDateSelection(),

                        Select::make('gender')
                            ->label('Gender')
                            ->options([
                                'Male' => 'Male',
                                'Female' => 'Female',
                            ])
                            ->nullable(),

                        TextInput::make('country')
                            ->label('Country')
                            ->default('Malaysia')
                            ->maxLength(255),

                        Textarea::make('notes_comments')
                            ->label('Notes/Comments')
                            ->nullable()
                            ->columnSpanFull(),

                        FileUpload::make('attachment')
                            ->label('Attachment')
                            ->multiple()
                            ->downloadable()
                            ->directory('attachments') // Saves under storage/app/public/attachments
                            ->nullable(),
                    ])
                    ->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->headerActions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\CreateAction::make()
                        ->icon('heroicon-o-plus')
                        ->color('primary'),
                    Tables\Actions\ImportAction::make('importBrands')
                        ->importer(CustomerImporter::class),
                    Tables\Actions\ExportAction::make()
                        ->exporter(CustomerExporter::class),
                ])
                    ->label('More actions')
                    ->icon('heroicon-m-ellipsis-vertical')
                    ->size(ActionSize::Small)
                    ->color('primary')
                    ->button(),
            ])
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Customer Name')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('house_unit_no')
                    ->label('House/Unit No.')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('telephone_no')
                    ->label('Telephone No.')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('address')
                    ->label('Address')
                    ->limit(50) // Truncate long addresses
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->sortable(), // Adds an email icon

                Tables\Columns\TextColumn::make('city_district')
                    ->label('City/District')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('ic_mykad')
                    ->label('IC/MyKad')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('postal_code')
                    ->label('Postal Code')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('date_of_birth')
                    ->label('Date of Birth')
                    ->dateTime()
                    ->sortable(),

                Tables\Columns\TextColumn::make('gender')
                    ->badge()
                    ->label('Gender')
                    ->colors([
                        'Male' => 'blue',
                        'Female' => 'pink',
                        'Other' => 'gray',
                    ])
                    ->sortable(),

                Tables\Columns\TextColumn::make('country')
                    ->label('Country')
                    ->sortable()
                    ->default('Malaysia'),

                Tables\Columns\TextColumn::make('notes_comments')
                    ->label('Notes/Comments')
                    ->limit(50)
                    ->toggleable(), // Can be hidden from table
                Tables\Columns\ImageColumn::make('attachment')
                    ->label('Attachment')
                    ->toggleable(), // Can be hidden from table
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
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    \STS\FilamentImpersonate\Tables\Actions\Impersonate::make()
                        ->hidden(function ($record) {
                            return !User::find($record->user_id);
                        }),
                    Tables\Actions\Action::make('Print PDF')
                        ->label('Print PDF')
                        ->color('primary')
                        ->icon('heroicon-o-printer')
                        ->url(fn($record) => route('print.invoice', $record->id))
                        ->openUrlInNewTab(),

                ])
                    ->label('More actions')
                    ->icon('heroicon-m-ellipsis-vertical')
                    ->size(ActionSize::Small)
                    ->color('primary')
                    ->button()

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
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCustomers::route('/'),
            'create' => Pages\CreateCustomer::route('/create'),
            'edit' => Pages\EditCustomer::route('/{record}/edit'),
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
