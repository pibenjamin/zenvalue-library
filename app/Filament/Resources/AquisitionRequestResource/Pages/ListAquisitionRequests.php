<?php

namespace App\Filament\Resources\AquisitionRequestResource\Pages;

use App\Filament\Resources\AquisitionRequestResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAquisitionRequests extends ListRecords
{
    protected static string $resource = AquisitionRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
            ->label('Créer une demande d\'acquisition')
            ->icon('heroicon-o-plus'),
        ];
    }
}
