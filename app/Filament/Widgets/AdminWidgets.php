<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use App\Models\Book;
use App\Models\User;
use App\Models\Tag;

use Filament\Support\Enums\IconPosition;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AdminWidgets extends BaseWidget
{
    protected static ?int $sort = 1; // Position après le FilamentInfoWidget

    protected function getHeading(): ?string
    {
        return 'Statistiques';
    }
     
    protected function getDescription(): ?string
    {
        return 'Un résumé des statistiques de l\'application.';
    }

    protected function getCards(): array
    {
        $activatedUsers         = User::where('password', 'NOT LIKE', '%zenvalue.fr%')->count();
        $sumUserSharingBooks    = Book::where('owner_id', 'IS NOT', null)->distinct()->count('owner_id');
        $booksWithoutCover      = Book::where('cover_url', null)->count();
        $booksWithoutISBN       = Book::where('isbn', null)->count();


        return [

//            Stat::make('', Book::count() . ' livres')
//                ->description('Total des livres répertoriés')
//                ->descriptionIcon('heroicon-m-book-open', IconPosition::Before)
//                ->chart([7, 2, 10, 3, 15, 4, 17])
//                ->color('success'),




//            Stat::make('# de tags', Tag::count()),
            Stat::make('# d\'utilisateurs activés / utilisateurs', $activatedUsers . ' / ' . User::count()),
            Stat::make('Combien sommes-nous à partager des livres ?', $sumUserSharingBooks . ' citizens'),
            Stat::make('# de livres sans couverture', $booksWithoutCover . ' soit ' . round($booksWithoutCover / Book::count() * 100) . '%'),
            Stat::make('# de livres sans ISBN', $booksWithoutISBN . ' soit ' . round($booksWithoutISBN / Book::count() * 100) . '%'),
        ];
    }

    public static function canView(): bool
    {
        return auth()->user()->hasRole('admin') || auth()->user()->hasRole('super_admin');
    }
}
