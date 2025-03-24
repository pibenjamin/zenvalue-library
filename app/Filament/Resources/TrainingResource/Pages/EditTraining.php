<?php

namespace App\Filament\Resources\TrainingResource\Pages;

use App\Filament\Resources\TrainingResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\TrainingResource\Widgets\BookTrainingsWidget;
use Illuminate\Support\Str;

class EditTraining extends EditRecord
{
    protected static string $resource = TrainingResource::class;

    protected function getFooterWidgets(): array
    {
        return [
            BookTrainingsWidget::class,
        ];
    }

}
