<?php

namespace App\Filament\Resources\AquisitionRequestResource\Pages;

use App\Filament\Resources\AquisitionRequestResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\CreateRecord;
use Filament\Forms\Components\Actions\ActionGroup;


class CreateAquisitionRequest extends CreateRecord
{
    protected static string $resource = AquisitionRequestResource::class;

// change the label of the create button
    protected function getCreateFormAction(): Action
    {
        return parent::getCreateFormAction()
            ->label('Soumettre la demande');
    }

}
