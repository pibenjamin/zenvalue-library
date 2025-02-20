<?php

namespace App\Livewire\Auth;

use Filament\Pages\Auth\Register as BaseRegister;
use Filament\Forms\Form;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use App\Models\User;
use Illuminate\Support\Facades\Notification;
use App\Notifications\NewUserCreated;
use Illuminate\Support\Facades\Validator;
use Exception;
use Illuminate\Validation\ValidationException;

class CustomRegister extends BaseRegister
{
    // ajouter des regles de validation supplémentaires
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label('Nom')
                    ->required()
                    ->maxLength(255),

                TextInput::make('email')
                    ->label('Courriel')
                    ->email()
                    ->required()
                    ->maxLength(255),

                TextInput::make('password')
                    ->label('Mot de passe')
                    ->helperText('Le mot de passe doit contenir au moins 8 caractères, une majuscule, une minuscule et un caractère spécial')
                    ->password()
                    ->required()
                    ->regex('/^(?=.*[a-z])(?=.*[A-Z])(?=.*[!@#$%^&*()\-_+=\[\]{};:\"\'\\,.<>\/?])[a-zA-Z!@#$%^&*()\-_+=\[\]{};:\"\'\\,.<>\/?]{8,}$/')
                    ->minLength(8)
                    ->maxLength(255),

                TextInput::make('password_confirmation')
                    ->label('Confirmation du mot de passe')
                    ->helperText('Le mot de passe doit contenir au moins 8 caractères, une majuscule, une minuscule et un caractère spécial')
                    ->password()
                    ->required()
                    ->regex('/^(?=.*[a-z])(?=.*[A-Z])(?=.*[!@#$%^&*()\-_+=\[\]{};:\"\'\\,.<>\/?])[a-zA-Z!@#$%^&*()\-_+=\[\]{};:\"\'\\,.<>\/?]{8,}$/')
                    ->minLength(8)
                    ->maxLength(255),

                // Example: Adding a custom role selection
//                Select::make('role')
//                    ->options([
//                        'admin' => 'Admin',
//                        'user' => 'User',
//                    ])
//                    ->required(),
            ]);
    }

    protected function handleRegistration(array $data): User
    {
        if(env('APP_ENV') == 'production'){
            if(!str_ends_with($data['email'], '@zenvalue.fr')){
                abort(403, 'Seules les adresses email @zenvalue.fr sont autorisées.');
            }
        }

        $user = User::create([
            'name'          => $data['name'],
            'email'         => $data['email'],
            'password'      => bcrypt($data['password']),
            'is_activated'  => false,
            'avatar'        => 'default-avatar.png',
        ]);

        if($user){

            $data['new_user_id']    = $user->id;
            $admin                  = User::where('email', config('app.admin_email'))->first();
            $admin->notify(new NewUserCreated($user));

            abort(403, 'Votre compte n\'est pas encore activé. Vous serez informé par email lorsque votre compte sera activé.');
        }   
    }
}