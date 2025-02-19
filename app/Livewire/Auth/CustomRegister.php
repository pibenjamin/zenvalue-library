<?php

namespace App\Livewire\Auth;

use Filament\Pages\Auth\Register as BaseRegister;
use Filament\Forms\Form;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use App\Models\User;
use Illuminate\Support\Facades\Notification;
use App\Notifications\NewUserCreated;
use Exception;
class CustomRegister extends BaseRegister
{
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
                    ->helperText('8 caractères minimum, une majuscule, une minuscule et un chiffre')
                    ->password()
                    ->required()
                    ->minLength(8)
                    ->maxLength(255),

                TextInput::make('password_confirmation')
                    ->label('Confirmation du mot de passe')
                    ->helperText('8 caractères minimum, une majuscule, une minuscule et un chiffre')
                    ->password()
                    ->required()
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
        $user = User::create([
            'name'          => $data['name'],
            'email'         => $data['email'],
            'password'      => bcrypt($data['password']),
            'is_activated'  => false,
            'avatar'        => 'default-avatar.png',
        ]);

        if($user){

            $data['new_user_id'] = $user->id;

            $admin = User::where('email', config('app.admin_email'))->first();
            $admin->notify(new NewUserCreated($user));

            abort(403, 'Votre compte n\'est pas encore activé. Vous serez informé par email lorsque votre compte sera activé.');


            //return $user;
        }   
    }
}
