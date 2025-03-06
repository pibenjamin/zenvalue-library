<?php

namespace App\Livewire\Auth;

use Filament\Pages\Auth\PasswordReset\ResetPassword;
use Filament\Forms\Form;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use App\Models\User;
use Illuminate\Support\Facades\Notification;
use App\Notifications\NewUserCreated;
use Illuminate\Support\Facades\Validator;
use Exception;
use Illuminate\Validation\ValidationException;

class CustomResetPassword extends ResetPassword
{
    public function mount(?string $email = null, ?string $token = null): void
    {
        parent::mount($token);
    }

    // ajouter des regles de validation supplémentaires
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('email')
                    ->label('Courriel')
                    ->disabled()
                    ->email()
                    ->required()
                    ->maxLength(255),

                TextInput::make('password')
                    ->label('Mot de passe')
                    ->helperText('Le mot de passe doit contenir au moins 8 caractères, un chiffre, une majuscule, une minuscule et un caractère spécial')
                    ->password()
                    ->required()
                    ->revealable()
                    ->regex('/^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[!@#$%^&*()\-_+=\[\]{};:\"\'\\,.<>\/?])[a-zA-Z0-9!@#$%^&*()\-_+=\[\]{};:\"\'\\,.<>\/?]{8,}$/')
                    ->minLength(8)
                    ->maxLength(255),

                TextInput::make('passwordConfirmation')
                    ->label('Confirmation du mot de passe')
                    ->helperText('Le mot de passe doit contenir au moins 8 caractères, un chiffre, une majuscule, une minuscule et un caractère spécial')
                    ->password()
                    ->revealable()
                    ->required()
                    ->regex('/^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[!@#$%^&*()\-_+=\[\]{};:\"\'\\,.<>\/?])[a-zA-Z0-9!@#$%^&*()\-_+=\[\]{};:\"\'\\,.<>\/?]{8,}$/')
                    ->minLength(8)
                    ->maxLength(255),
            ]);
    }
}
