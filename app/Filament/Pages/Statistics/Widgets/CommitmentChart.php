<?php

namespace App\Filament\Pages\Statistics\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Comment;
use App\Models\Rating;
use App\Models\Loan;
use App\Models\AquisitionRequest;
use App\Models\Book;
use Filament\Support\RawJs;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use Carbon\Carbon;

class CommitmentChart extends ChartWidget
{
    protected static ?string $heading = 'Taux d\'engagement des utilisateurs';
    protected int | string | array $columnSpan = 'full';
    protected static ?string $maxHeight = '300px';

    protected function getData(): array
    {
        $dataComments = Trend::model(Comment::class)
            ->between(
                start: now()->startOfYear(),
                end: now()->endOfYear(),
            )
            ->perMonth()
            ->count();

        $dataRatings = Trend::model(Rating::class)
            ->between(
                start: now()->startOfYear(),
                end: now()->endOfYear(),
            )
            ->perMonth()
            ->count();

        $dataLoan = Trend::model(Loan::class)
            ->between(
                start: now()->startOfYear(),
                end: now()->endOfYear(),
            )
            ->perMonth()
            ->count();


        $dataUserAquisitionDemands = Trend::model(AquisitionRequest::class)
            ->between(
                start: now()->startOfYear(),
                end: now()->endOfYear(),
            )
            ->perMonth()    
            ->count();

        $dataSharingHerBooks = Trend::model(Book::class)
            ->between(
                start: now()->startOfYear(),
                end: now()->endOfYear(),
            )
            ->perMonth()
            ->count();

        return [
            'datasets' => [
                [
                    'label' => 'Commentaires',
                    'data' => $dataComments->map(fn (TrendValue $value) => $value->aggregate),
                    'borderColor' => '#4CAF50',
                    'fill' => true,
                    'backgroundColor' => 'rgba(76, 175, 80, 0.1)',
                ],
                [
                    'label' => 'Notes',
                    'data' => $dataRatings->map(fn (TrendValue $value) => $value->aggregate),
                    'borderColor' => '#FFC107',
                    'fill' => true,
                    'backgroundColor' => 'rgba(255, 193, 7, 0.1)',
                ],  
                [
                    'label' => 'Prêts',
                    'data' => $dataLoan->map(fn (TrendValue $value) => $value->aggregate),
                    'borderColor' => '#2196F3',
                    'fill' => true,
                    'backgroundColor' => 'rgba(33, 150, 243, 0.1)',
                ],
                [
                    'label' => 'Demandes d\'acquisition',
                    'data' => $dataUserAquisitionDemands->map(fn (TrendValue $value) => $value->aggregate),
                    'borderColor' => '#FF5722',
                    'fill' => true,
                    'backgroundColor' => 'rgba(255, 87, 34, 0.1)',
                ],
                [
                    'label' => 'Partage de livres',
                    'data' => $dataSharingHerBooks->map(fn (TrendValue $value) => $value->aggregate),
                    'borderColor' => '#9C27B0',
                    'fill' => true,
                    'backgroundColor' => 'rgba(156, 39, 176, 0.1)',
                ],      
            ],
            'labels' => $dataComments->map(fn (TrendValue $value) => Carbon::parse($value->date)->locale('fr_FR')->format('F Y')),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
