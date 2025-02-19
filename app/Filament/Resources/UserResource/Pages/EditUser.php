<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use App\Notifications\UserActivated;
use Filament\Forms\Components\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Actions\ActionGroup;
use App\Models\Role;


class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getFormActions(): array
    {
        
        return [
            $this->getSaveFormAction(),
            Actions\Action::make('ActivateAndNotify')
                ->label('Activer et envoyer un email')
                ->action('ActivateAndNotify')
                ->color('primary')
                ->disabled(fn () => $this->record->is_activated),





            $this->getCancelFormAction(),
        ];
    }

    public function ActivateAndNotify()
    {
        $this->save(true);

        $this->record->is_activated = true;

        $role = Role::where('name', 'user')->first();

        $this->record->roles()->attach($role);

        $this->record->save();

        $this->record->notify(new UserActivated($this->record));

        $this->refreshFormData(['is_activated' => true, 'roles' => [$role]]);

    }
}
