<?php

namespace App\Filament\Resources\TrainingResource\Pages;

use App\Filament\Resources\TrainingResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\TrainingResource\Widgets\BookTrainings;
class CreateTraining extends CreateRecord
{
    protected static string $resource = TrainingResource::class;

    public function getHeaderWidgets(): array
    {
        return [
            BookTrainings::class,
        ];
    }
}
