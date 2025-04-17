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


class ProgressBookReadingWidget extends BaseWidget
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
        
        // Récupère les IDs des livres actuellement en cours de lecture
        $userInProgressLoans = $user->loans->where('status', Loan::STATUS_IN_PROGRESS)->pluck('book_id')->toArray();

        // Calcule le nombre de livres du parcours actuellement en cours de lecture
        $readInProgressBooks = count(array_intersect($userParcoursBooks, $userInProgressLoans));
        
        // Calcule le pourcentage de livres en cours de lecture
        $percentageInProgress = $totalBooks > 0 ? round(($readInProgressBooks / $totalBooks) * 100) : 0;


        return [
            Stat::make('Lectures en cours', $percentageInProgress . '%')
                ->description("$readInProgressBooks livres en cours de lecture sur $totalBooks")
                ->chart([0, min($percentageInProgress, 100)]),                

        ];
    }
}
