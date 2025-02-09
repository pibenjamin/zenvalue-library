<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Book;
use App\Models\User;

class AdminWidgets extends BaseWidget
{

    protected static ?int $sort = 1; // Position après le FilamentInfoWidget

    protected function getCards(): array
    {
        return [
            Stat::make('Nombre de livres', Book::count()),
            Stat::make('Nombre d\'utilisateurs', User::count()),
        ];
    }

    public static function canView(): bool
    {
        return auth()->user()->hasRole('admin') || auth()->user()->hasRole('super_admin');
    }
}
