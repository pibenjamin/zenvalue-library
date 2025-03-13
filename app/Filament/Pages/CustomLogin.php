<?php

namespace App\Filament\Pages;

use Filament\Facades\Filament;
use Filament\Pages\Auth\Login;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\TextInput;
use Illuminate\Contracts\Support\Htmlable;
use Filament\Http\Responses\Auth\LoginResponse;
use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;
use Illuminate\Validation\ValidationException;



class CustomLogin extends Login
{
    public function mount(): void
    {
        if (Filament::auth()->check()) {
            redirect()->intended(Filament::getUrl());
        }

//        if (app()->environment('local')) {
//            $this->form->fill([
//                'email' => 'email',
//                'password' => 'password',
//            ]);
//        }
    }

    public function authenticate(): LoginResponse
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
        $user->last_login_at = now();
        $user->save();
        
        if (! $user->is_activated) {
            Filament::auth()->logout();

            throw ValidationException::withMessages([
                'data.email' => __('Votre compte n\'est pas encore activé.'),
            ]);
        }

        session()->regenerate();

        return app(LoginResponse::class);
    }

    protected function getCredentialsFromFormData(array $data): array
    {
        return [
            'email' => $data['email'],
            'password' => $data['password'],
        ];
    }

    protected function getFormSchema(): array
    {
        return [
            $this->getEmailFormComponent(),
            $this->getPasswordFormComponent(),
        ];
    }

//    public function getTitle(): string|Htmlable
//    {
//        return __('Admin Login');
//    }

//    public function getHeading(): string|Htmlable
//    {
//        return __('Admin Login');
//    }


    protected function getEmailFormComponent(): Component
    {
        return TextInput::make('email')
            ->label('Courriel')
            ->required()
            ->autofocus()
            ->extraInputAttributes(['tabindex' => 1])
            ->autocomplete();
    }
}