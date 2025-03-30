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

        $authorOnIndexedBooks = Author::whereHas('books', function ($query) {
            $query->whereIn('status', [Book::STATUS_ON_SHELF, Book::STATUS_BORROWED])
                ->where('missing', false);
        })->count();

        $tagOnIndexedBooks = Tag::whereHas('books', function ($query) {
            $query->whereIn('status', [Book::STATUS_ON_SHELF, Book::STATUS_BORROWED])
                ->where('missing', false);
        })->count();


        return [
            Stat::make('', Book::where('status', Book::STATUS_ON_SHELF)->where('missing', false)->count() . ' livres')
                ->description('Total des livres sur étagère')
                ->color('primary')
                ->descriptionIcon('heroicon-m-book-open', IconPosition::Before),
            Stat::make('', $authorOnIndexedBooks . ' auteurs')
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
