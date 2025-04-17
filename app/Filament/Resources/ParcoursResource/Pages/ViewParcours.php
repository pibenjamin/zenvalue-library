<?php

namespace App\Filament\Resources\ParcoursResource\Pages;

use App\Filament\Resources\ParcoursResource;
use App\Filament\Resources\ParcoursResource\Widgets\BookParcoursWidget;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewParcours extends ViewRecord
{
    protected static string $resource = ParcoursResource::class;

    protected function getFooterWidgets(): array
    {
        return [
            BookParcoursWidget::class,
        ];
    }
}