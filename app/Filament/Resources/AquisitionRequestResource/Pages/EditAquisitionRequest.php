<?php

namespace App\Filament\Resources\AquisitionRequestResource\Pages;

use App\Filament\Resources\AquisitionRequestResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAquisitionRequest extends EditRecord
{
    protected static string $resource = AquisitionRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
