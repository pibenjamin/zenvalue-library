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

class ProgressBooksFinishedWidget extends BaseWidget
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

    protected function getColumns(): int
    {
        return 2;
    }

    protected function getCards(): array
    {
        $user           = auth()->user();
        $userParcours   = $user->parcours->first();

        $userParcoursBooks = $userParcours->books()
            ->orderBy('parcours_order')
            ->limit(3)
            ->pluck('books.id')
            ->toArray();

        // Nombre total de livres dans le parcours
        $totalBooks = count($userParcoursBooks);

        // Récupère les IDs des livres qui ont été retournés par l'utilisateur
        $userReturnedLoans = $user->loans->where('status', Loan::STATUS_RETURNED)->pluck('book_id')->toArray();
        
        // Calcule le nombre de livres du parcours qui ont été lus (retournés)
        $readBooks = count(array_intersect($userParcoursBooks, $userReturnedLoans));
    
        // Calcule le pourcentage de livres lus, arrondi à l'entier le plus proche
        $percentageRead = $totalBooks > 0 ? round(($readBooks / $totalBooks) * 100) : 0;        

        return [
            Stat::make('Lectures terminées', $percentageRead . '%')
                ->description("$readBooks livres lus sur $totalBooks")
                ->chart([0, min($percentageRead, 100)]),
        ];
    }
}
