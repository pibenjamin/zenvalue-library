<?php

namespace App\Filament\Resources\TutorielResource\Pages;

use App\Filament\Resources\TutorielResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTutoriels extends ListRecords
{
    protected static string $resource = TutorielResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
