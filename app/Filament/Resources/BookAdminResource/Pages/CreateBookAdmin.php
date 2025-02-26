<?php

namespace App\Filament\Resources\BookAdminResource\Pages;

use App\Filament\Resources\BookAdminResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateBookAdmin extends CreateRecord
{
    protected static string $resource = BookAdminResource::class;
}
