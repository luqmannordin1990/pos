<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\User;
use Filament\Tables;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Filament\Support\Enums\ActionSize;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\Rules\Password;
use App\Filament\Resources\UserResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\UserResource\RelationManagers;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    // protected static ?string $navigationGroup = 'Maintenance';
    // protected static ?string $navigationLabel = 'Maintenance';
    protected static ?string $navigationGroup = 'Maintenance';
    protected static ?int $navigationSort = 4;

    public static function shouldRegisterNavigation(): bool
    {
        if (auth()->user()->hasRole('admin')) {
            return true;
        }
        return false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\Select::make('search_staff')
                            ->prefix('LPPSA')
                            ->suffixIcon('heroicon-m-magnifying-glass')
                            ->options(function (Get $get) {
                                $finduserlppsa = DB::connection("staffdb")
                                    ->table("user_ns")->get();
                                $finduser = collect($finduserlppsa)->map(function ($item, $key) {
                                    $item->display = str_replace('LPPSA', "", $item->lppsa_no);
                                    $item->store = $item->name . '###' . $item->email . '###' . $item->username;
                                    return $item;
                                })->pluck('display', 'store')->toArray();
                                return $finduser;
                            })
                            ->live(onBlur: true)
                            ->preload()
                            ->searchable()
                            ->label('LPPSA ID')
                            ->dehydrated(false)
                            ->afterStateUpdated(function (Set $set, Get $get, $state) {
                                $finduser = explode('###', $get('search_staff'));
                                if (isset($finduser[0])) {
                                    $set('name', $finduser[0]);
                                    $set('email', $finduser[1]);
                                    $set('username', $finduser[2]);
                                }
                            }),

                    ])->columns('full')
                    ->visible(fn($operation) =>  $operation == 'create'),
                Forms\Components\Section::make()
                    ->schema([

                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->readonly(),
                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255)
                            ->readonly(),
                        Forms\Components\TextInput::make('username')
                            ->required()
                            ->readonly(),
                        Forms\Components\DateTimePicker::make('email_verified_at')
                            ->visible(false),
                        Forms\Components\TextInput::make('password')
                            ->dehydrateStateUsing(fn(string $state): string => Hash::make($state))
                            ->dehydrated(fn(?string $state): bool => filled($state))
                            ->required(fn(string $operation): bool => $operation === 'create')
                            ->password()
                            ->confirmed()
                            ->revealable()
                            ->maxLength(255)
                            ->rule(Password::default())
                            ->visible(true),
                        Forms\Components\TextInput::make('password_confirmation')
                            ->label('Confirm password')
                            ->password()
                            ->revealable()
                            ->required(fn(string $operation): bool => $operation === 'create')
                            ->visible(true),
                        Forms\Components\CheckboxList::make('roles')
                            ->required()
                            ->relationship(name: 'roles', titleAttribute: 'name')
                            ->saveRelationshipsUsing(function (Model $record, $state) {
                                $newRole = Role::whereIn('id', $state)->get();
                                $record->syncRoles([]);
                                $record->assignRole($newRole);
                            })
                            ->columns(2),
                    ])->columns(2),

            ])->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('roles.name')
                    ->badge()
                    ->searchable(),
                Tables\Columns\TextColumn::make('email_verified_at')
                    ->dateTime()
                    ->sortable(),
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
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make()
                        ->hidden(fn($record) => auth()->user()->id == $record->id),
                    Tables\Actions\ForceDeleteAction::make()
                        ->hidden(fn($record) => auth()->user()->id == $record->id),
                    Tables\Actions\RestoreAction::make()
                        ->hidden(fn($record) => auth()->user()->id == $record->id),

                ])
                    ->label('More actions')
                    ->icon('heroicon-m-ellipsis-vertical')
                    ->size(ActionSize::Small)
                    ->color('primary')
                    ->button(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ])
            ->recordUrl(
                fn(Model $record): string => UserResource::getUrl('edit', ['record' => $record->id])
            )
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
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
