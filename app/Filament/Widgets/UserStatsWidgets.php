<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Book;
use App\Models\User;
use App\Models\Author;

class UserStatsWidgets extends BaseWidget
{

    protected static ?int $sort = 1; // Position après le FilamentInfoWidget

    protected function getCards(): array
    {
        return [
            Stat::make('Nombre de livres', Book::count()),
            Stat::make('Nombre d\'auteurs', Author::count()),
            Stat::make('Nombre de tags', Tag::count()),



            
        ];
    }

    public static function canView(): bool
    {
        return auth()->user()->hasRole('user');
    }
}
