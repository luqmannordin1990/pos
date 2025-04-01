<?php

namespace App\Filament\Guest\Pages;

use Livewire\Component;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Facades\Filament;
use Illuminate\Support\HtmlString;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Blade;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Components\TextInput;

use Illuminate\Contracts\Support\Htmlable;
use Filament\Models\Contracts\FilamentUser;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Concerns\InteractsWithForms;

use DanHarrin\LivewireRateLimiting\WithRateLimiting;
use Filament\Http\Responses\Auth\Contracts\LoginResponse;
use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;
use Illuminate\Validation\ValidationException;

class GuestLogin extends Page implements HasForms
{
    use WithRateLimiting;
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.guest.pages.guest-login';
    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $slug = '/login';
    public function getTitle(): string | Htmlable
    {
        return __('');
    }

    use InteractsWithForms;

    public ?array $data = [];

    
    public function mount()
    {
        if (Filament::auth()->check()) {
            // $this->redirect(filament()->getPanel('main')->getLoginUrl(), navigate: false);
            $this->dispatch('reload-page', url('/main')); 
     
            // return  redirect(filament()->getPanel('main')->getLoginUrl());
            // redirect()->intended(filament()->getPanel('main')->getLoginUrl());
        }

        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                \Filament\Forms\Components\Section::make('Login')
                    ->schema([
                        \Filament\Forms\Components\TextInput::make('email')
                            ->label(__('filament-panels::pages/auth/login.form.email.label'))
                            ->email()
                            ->required()
                            ->autocomplete()
                            ->autofocus()
                            ->extraInputAttributes(['tabindex' => 1]),
                        \Filament\Forms\Components\TextInput::make('password')
                            ->label(__('filament-panels::pages/auth/login.form.password.label'))
                            ->hint(new HtmlString(Blade::render('<x-filament::link href="'.url('/guest/password-reset').'" tabindex="3"> {{ __(\'filament-panels::pages/auth/login.actions.request_password_reset.label\') }}</x-filament::link>')) )
                            ->password()
                            ->revealable(filament()->arePasswordsRevealable())
                            ->autocomplete('current-password')
                            ->required()
                            ->extraInputAttributes(['tabindex' => 2]),
                        \Filament\Forms\Components\Checkbox::make('remember')
                            ->label(__('filament-panels::pages/auth/login.form.remember.label')),
                    ])
                    ->footerActions([
                        \Filament\Forms\Components\Actions\Action::make('login')
                            ->action(function () {
                                $this->authenticate();
                            }),
                    ])


            ])

            ->statePath('data');
    }

    public function authenticate()
    {
        try {
            $this->rateLimit(5);
        } catch (TooManyRequestsException $exception) {
            $this->getRateLimitedNotification($exception)?->send();

            return null;
        }

        $data = $this->form->getState();

        if (! Filament::auth()->attempt($this->getCredentialsFromFormData($data), $data['remember'] ?? false)) {
            $this->throwFailureValidationException();
        }

        $user = Filament::auth()->user();

        if (
            ($user instanceof FilamentUser) &&
            (! $user->canAccessPanel(Filament::getCurrentPanel()))
        ) {
            Filament::auth()->logout();

            $this->throwFailureValidationException();
        }

        session()->regenerate();


        return redirect(filament()->getPanel('main')->getLoginUrl());
     
    }

    protected function getCredentialsFromFormData(array $data): array
    {
        return [
            'email' => $data['email'],
            'password' => $data['password'],
        ];
    }

    protected function throwFailureValidationException(): never
    {
        throw ValidationException::withMessages([
            'data.email' => __('filament-panels::pages/auth/login.messages.failed'),
        ]);
    }
}
