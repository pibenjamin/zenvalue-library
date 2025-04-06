<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use App\Models\Book;
use App\Models\User;
use App\Models\Tag;
use App\Models\Comment;
use App\Models\Rating;
use App\Models\Loan;
use Filament\Support\Enums\IconPosition;
use Filament\Widgets\StatsOverviewWidget\Stat;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;

class AdminWidgets extends BaseWidget
{
    use HasWidgetShield;

    protected static ?int $sort = 1; // Position après le FilamentInfoWidget



    protected function getHeading(): ?string
    {
        return 'Statistiques (admin)';
    }
     
    protected function getDescription(): ?string
    {
        return 'Un résumé des statistiques de l\'application.';
    }

    protected function getCards(): array
    {
        $activatedUsers         = User::where('password', 'LIKE', '%$2y$%')->count();
        $sumUserSharingBooks    = Book::where('owner_id', 'IS NOT', null)->distinct()->count('owner_id');

        $indexedBooks           = Book::whereIn('status', [Book::STATUS_ON_SHELF, Book::STATUS_BORROWED])
                                ->where('missing', false)
                                ->count();

        $booksWithCover         = Book::where('cover_url', '!=', null)
                                ->whereIn('status', [Book::STATUS_ON_SHELF, Book::STATUS_BORROWED])
                                ->where('missing', false)
                                ->count();
        $booksWithISBN          = Book::where('isbn', '!=', null)
                                ->whereIn('status', [Book::STATUS_ON_SHELF, Book::STATUS_BORROWED])
                                ->where('missing', false)
                                ->count();

        $booksWithDescription   = Book::where('description', '!=', null)
                                ->whereIn('status', [Book::STATUS_ON_SHELF, Book::STATUS_BORROWED])
                                ->where('missing', false)
                                ->count();

        $booksWithTags         = Book::whereIn('status', [Book::STATUS_ON_SHELF, Book::STATUS_BORROWED])
                                ->where('missing', false)
                                ->whereHas('tags')
                                ->count();       

        return [

            Stat::make('# d\'utilisateurs activés', $activatedUsers . ' / ' . User::count()),
            Stat::make('Combien sommes-nous à partager des livres ?', $sumUserSharingBooks . ' citizens'),
            Stat::make('# de livres avec couverture', $booksWithCover . ' soit ' . round($booksWithCover / $indexedBooks * 100) . '%'),
            Stat::make('# de livres avec ISBN', $booksWithISBN . ' soit ' . round($booksWithISBN / $indexedBooks * 100) . '%'),
            Stat::make('# de livres avec description', $booksWithDescription . ' soit ' . round($booksWithDescription / $indexedBooks * 100) . '%'),
            Stat::make('# de livres avec tags', $booksWithTags . ' soit ' . round($booksWithTags / $indexedBooks * 100) . '%'),
        ];
    }
}
