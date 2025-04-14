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
use App\Models\User;
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

        $adminIds = User::whereIn('email', [
            config('app.admin_email'),
            'gf@zenvalue.fr',
        ])->pluck('id');

        $adminWithGF = User::whereIn('email', [
            config('app.admin_email'),
        ])->pluck('id');


        $dataSharingHerBooks = Trend::query(
            Book::query()->whereNotIn('owner_id', $adminIds)
        )
            ->between(
                start: now()->startOfYear(),
                end: now()->endOfYear(),
            )
            ->perMonth()
            ->count();

        $dataSharingHerBooksWithGF = Trend::query(
            Book::query()->whereNotIn('owner_id', $adminWithGF)
        )
            ->between(
                start: now()->startOfYear(),
                end: now()->endOfYear(),
            )
            ->perMonth()
            ->count();

        return [
            'datasets' => [
                [
                    'label' => 'Engagement global',
                    'data' => $dataComments->map(function (TrendValue $value, $key) use (
                        $dataRatings, 
                        $dataLoan, 
                        $dataUserAquisitionDemands, 
                        $dataSharingHerBooks,
                        $dataSharingHerBooksWithGF
                    ) {
                        return $value->aggregate 
                            + $dataRatings[$key]->aggregate 
                            + $dataLoan[$key]->aggregate 
                            + $dataUserAquisitionDemands[$key]->aggregate 
                            + $dataSharingHerBooks[$key]->aggregate
                            + $dataSharingHerBooksWithGF[$key]->aggregate;
                    }),
                    'borderColor' => '#A0A0A0',
                    'fill' => true,
                    'backgroundColor' => 'rgba(160, 160, 160, 0.1)',
                ],
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
                [
                    'label' => 'Partage de livres avec GF',
                    'data' => $dataSharingHerBooksWithGF->map(fn (TrendValue $value) => $value->aggregate),
                    'borderColor' => '#E91E63',
                    'fill' => true,
                    'backgroundColor' => 'rgba(233, 30, 99, 0.1)',
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
