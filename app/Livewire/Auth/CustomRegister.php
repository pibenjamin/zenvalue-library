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
use Filament\Forms\Components\Checkbox;
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
                    ->helperText('Le mot de passe doit contenir au moins 8 caractères, un chiffre, une majuscule, une minuscule et un caractère spécial')
                    ->password()
                    ->required()
                    ->regex('/^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[!@#$%^&*()\-_+=\[\]{};:\"\'\\,.<>\/?])[a-zA-Z0-9!@#$%^&*()\-_+=\[\]{};:\"\'\\,.<>\/?]{8,}$/')
                    ->minLength(8)
                    ->maxLength(255),

                TextInput::make('password_confirmation')
                    ->label('Confirmation du mot de passe')
                    ->helperText('Le mot de passe doit contenir au moins 8 caractères, un chiffre, une majuscule, une minuscule et un caractère spécial')
                    ->password()
                    ->required()
                    ->regex('/^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[!@#$%^&*()\-_+=\[\]{};:\"\'\\,.<>\/?])[a-zA-Z0-9!@#$%^&*()\-_+=\[\]{};:\"\'\\,.<>\/?]{8,}$/')
                    ->minLength(8)
                    ->maxLength(255),

                Checkbox::make('accept_terms')
                    ->label('En cochant cette case, je m\'engage à déclarer tout emprunt de livres via l\'application.')
                    ->required()
                    ->inline(false),
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