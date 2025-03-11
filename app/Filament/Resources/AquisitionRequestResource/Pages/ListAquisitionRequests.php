<?php

namespace App\Filament\Resources\AquisitionRequestResource\Pages;

use App\Filament\Resources\AquisitionRequestResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAquisitionRequests extends ListRecords
{
    protected static string $resource = AquisitionRequestResource::class;

    public function getTitle(): string
    {
        return 'Liste des demandes d\'acquisition';
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
            ->label('Faire une demande d\'acquisition')
            ->icon('heroicon-o-plus'),
        ];
    }
}
