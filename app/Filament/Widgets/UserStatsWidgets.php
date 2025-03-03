<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Book;
use App\Models\User;
use App\Models\Author;
use App\Models\Tag;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Support\Enums\IconPosition;


class UserStatsWidgets extends BaseWidget
{
    use HasWidgetShield;

    protected static ?int $sort = 1; // Position après le FilamentInfoWidget

    protected function getHeading(): ?string
    {
        return __('Statistiques de l\'application');
    }

    protected function getCards(): array
    {
        return [
            Stat::make('', Book::count() . ' livres')
                ->description('Total des livres répertoriés')
                ->color('primary')
                ->descriptionIcon('heroicon-m-book-open', IconPosition::Before),
            Stat::make('', Author::count() . ' auteurs')
                ->description('Total des auteurs répertoriés')
                ->color('primary')
                ->descriptionIcon('heroicon-m-user-group', IconPosition::Before),
            Stat::make('', Tag::count() . ' mots-clés')
                ->description('Total des mots-clés répertoriés')
                ->color('primary')
                ->descriptionIcon('heroicon-m-tag', IconPosition::Before),
        ];
    }

}
