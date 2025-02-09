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
            Stat::make('Nombre d\'utilisateurs activés', User::where('updated_at', '>=', env('APP_RELEASE_DATE'))->count()),
            Stat::make('Combien sommes-nous à partager des livres ?', Book::where('owner_id', '!=', null)->distinct()->count('owner_id')),



            
        ];
    }

    public static function canView(): bool
    {
        return auth()->user()->hasRole('user');
    }
}
