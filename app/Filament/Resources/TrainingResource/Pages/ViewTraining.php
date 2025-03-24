<?php

namespace App\Filament\Resources\TrainingResource\Pages;

use App\Filament\Resources\TrainingResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use App\Filament\Resources\TrainingResource\Widgets\BookTrainingsWidget;
class ViewTraining extends ViewRecord
{
    protected static string $resource = TrainingResource::class;


    protected function getFooterWidgets(): array
    {
        return [
            BookTrainingsWidget::class,
        ];
    }

}
