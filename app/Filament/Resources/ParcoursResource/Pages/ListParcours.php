<?php

namespace App\Filament\Resources\ParcoursResource\Pages;

use App\Filament\Resources\ParcoursResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use App\Models\Book;
use App\Models\Parcours;
use Filament\Forms;

class ListParcours extends ListRecords
{
    protected static string $resource = ParcoursResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
        ];

    }
    
}
