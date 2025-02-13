<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Book;
use App\Models\User;
use App\Models\Tag;

class AdminWidgets extends BaseWidget
{

    protected static ?int $sort = 1; // Position après le FilamentInfoWidget

    protected function getCards(): array
    {
        return [
            Stat::make('# de livres', Book::count()),
            Stat::make('# de tags', Tag::count()),
            Stat::make('# d\'utilisateurs activés / utilisateurs', User::where('updated_at', '>=', env('APP_RELEASE_DATE'))->count() . ' / ' . User::count()),
            Stat::make('Combien sommes-nous à partager des livres ?', Book::where('owner_id', 'IS NOT', null)->distinct()->count('owner_id')),
            Stat::make('# de livres sans couverture', Book::where('cover_url', null)->count() . ' soit ' . round(Book::where('cover_url', null)->count() / Book::count() * 100) . '%'),
            Stat::make('# de livres sans ISBN', Book::where('isbn', null)->count() . ' soit ' . round(Book::where('isbn', null)->count() / Book::count() * 100) . '%'),
        ];
    }

    public static function canView(): bool
    {
        return auth()->user()->hasRole('admin') || auth()->user()->hasRole('super_admin');
    }
}
