<?php

namespace App\Filament\Resources\TrainingResource\Pages;

use App\Filament\Resources\TrainingResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\TrainingResource\Widgets\BookTrainings;
use Illuminate\Support\HtmlString;
use App\Services\TrainingImport;

class ListTrainings extends ListRecords
{
    protected static string $resource = TrainingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Actions\Action::make('importTrainings')
                ->label('Importer les formations')
                ->icon('heroicon-o-arrow-up-tray')
                ->action(function () {
                    app(TrainingImport::class)->import();
                })
                ->visible(fn () => auth()->user()->hasRole('super_admin')),
        ];
    }


    public function getHeaderWidgets(): array
    {
        return [
        ];
    }


}
