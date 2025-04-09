<?php

namespace App\Filament\Auth;

use Filament\Pages\Page;
use Filament\Pages\Auth\EditProfile as oriEditProfile;

class EditProfile extends oriEditProfile
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $layout = 'filament-panels::components.layout.simple';
    protected static string $view = 'filament.pages.edit-profile';

    protected function getForms(): array
    {
        return [
            'form' => $this->form(
                $this->makeForm()
                    ->schema([
                        $this->getNameFormComponent(),
                        $this->getEmailFormComponent(),
                        \Filament\Forms\Components\TextInput::make('phone')
                            ->label('Phone Number')
                            ->required()
                            ->numeric()
                            ->minLength(10) // Adjust based on your country's phone number length
                            ->maxLength(15) // Prevent excessively long numbers
                            ->placeholder('Enter phone number: 0123456789'),

                        $this->getPasswordFormComponent(),
                        $this->getPasswordConfirmationFormComponent(),
                    ])
                    ->operation('edit')
                    ->model($this->getUser())
                    ->statePath('data')
                    ->inlineLabel(! static::isSimple()),
            ),
        ];
    }
}
