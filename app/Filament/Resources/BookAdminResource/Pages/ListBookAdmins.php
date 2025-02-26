<?php

namespace App\Filament\Resources\BookAdminResource\Pages;

use App\Filament\Resources\BookAdminResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBookAdmins extends ListRecords
{
    protected static string $resource = BookAdminResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
