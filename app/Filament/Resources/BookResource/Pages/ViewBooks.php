<?php

namespace App\Filament\Resources\BookResource\Pages;

use App\Filament\Resources\BookResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewBooks extends ViewRecord
{
    protected static string $resource = BookResource::class;

    
    protected function getActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }




 
}
