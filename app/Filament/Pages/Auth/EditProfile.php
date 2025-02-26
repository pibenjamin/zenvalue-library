<?php
 
namespace App\Filament\Pages\Auth;
 
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Pages\Auth\EditProfile as BaseEditProfile;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;

use Filament\Forms\Components\Component;
 


class EditProfile extends BaseEditProfile
{
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                $this->getNameFormComponent(),
                $this->getEmailFormComponent(),
                $this->getPasswordFormComponent(),
                Forms\Components\FileUpload::make('avatar')
                ->avatar()
                ->directory('avatars'),
            

                $this->getPasswordConfirmationFormComponent(),
            ]);
    }


    protected function getNameFormComponent(): Component
    {
        return parent::getNameFormComponent()
            ->required()
            ->maxLength(25);
    }
}