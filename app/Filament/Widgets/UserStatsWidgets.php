<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Book;
use App\Models\User;
use App\Models\Author;
use App\Models\Tag;

class UserStatsWidgets extends BaseWidget
{
    protected static ?int $sort = 1; // Position après le FilamentInfoWidget

    protected function getHeading(): ?string
    {
        return __('Statistiques de l\'application');
    }

    protected function getCards(): array
    {
        return [
            Stat::make('', Book::count() . ' livres'),
            Stat::make('', Author::count() . ' auteurs'),
            Stat::make('', Tag::count() . ' mots-clés'),
        ];
    }

    public static function canView(): bool
    {
        return auth()->user()->hasRole('user');
    }
}
