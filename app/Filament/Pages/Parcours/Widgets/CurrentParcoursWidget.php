<?php

namespace App\Filament\Pages\Parcours\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Book;
use App\Models\User;
use App\Models\Parcours;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Support\Enums\IconPosition;
use Filament\Widgets\Widget;
use Illuminate\Support\HtmlString;
use App\Models\Loan;

class CurrentParcoursWidget extends BaseWidget
{
    use HasWidgetShield;

    protected static ?int $sort = 1; // Position après le FilamentInfoWidget

    protected function getHeading(): ?string
    {
        return ' ';
    }

    protected function getDescription(): ?string
    {
        return ' ';
    }

    protected function getCards(): array
    {
        $user           = auth()->user();
        $userParcours   = $user->parcours->first();

        return [
            Stat::make('', $userParcours->name)
                ->description('Vous êtes actuellement inscrit à ce parcours')
                ->descriptionIcon('heroicon-m-map-pin', IconPosition::Before)
                ->descriptionColor('success'),
        ];
    }
}
