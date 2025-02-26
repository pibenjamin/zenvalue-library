<?php

namespace App\Filament\Resources\BookAdminResource\Pages;

use App\Filament\Resources\BookAdminResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBookAdmin extends EditRecord
{
    protected static string $resource = BookAdminResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
