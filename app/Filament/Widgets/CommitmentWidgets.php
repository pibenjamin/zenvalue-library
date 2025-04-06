<?php

namespace App\Filament\Widgets;

use App\Models\Comment;
use App\Models\Rating;
use App\Models\Loan;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class CommitmentWidgets extends BaseWidget
{
    protected function getHeading(): string
    {
        return "Taux d'engagement des utilisateurs (nombre d'actions / nombre d'utilisateurs)";
    }

    protected function getStats(): array
    {
        $activatedUsers = User::where('password', 'LIKE', '%$2y$%')->count();
        $totalComments = Comment::count();
        $totalRatings = Rating::count();
        $totalLoans = Loan::count();

        $commentsCommitmentRate = round($totalComments / $activatedUsers * 100);
        $ratingsCommitmentRate = round($totalRatings / $activatedUsers * 100);
        $loansCommitmentRate = round($totalLoans / $activatedUsers * 100);

        return [
            Stat::make('Taux de commentaires', $commentsCommitmentRate . '%')
                ->description(
                    $this->getEngagementDescription('comments', $commentsCommitmentRate) . "\n" .
                    "({$totalComments} commentaires / {$activatedUsers} utilisateurs)"
                )
                ->descriptionIcon($this->getEngagementIcon($commentsCommitmentRate, 'comments'))
                ->color($this->getEngagementColor($commentsCommitmentRate, 'comments')),

            Stat::make('Taux de notes', $ratingsCommitmentRate . '%')
                ->description(
                    $this->getEngagementDescription('ratings', $ratingsCommitmentRate) . "\n" .
                    "({$totalRatings} notes / {$activatedUsers} utilisateurs)"
                )
                ->descriptionIcon($this->getEngagementIcon($ratingsCommitmentRate, 'ratings'))
                ->color($this->getEngagementColor($ratingsCommitmentRate, 'ratings')),

            Stat::make("Taux d'emprunts", $loansCommitmentRate . '%')
                ->description(
                    $this->getEngagementDescription('loans', $loansCommitmentRate) . "\n" .
                    "({$totalLoans} emprunts / {$activatedUsers} utilisateurs)"
                )
                ->descriptionIcon($this->getEngagementIcon($loansCommitmentRate, 'loans'))
                ->color($this->getEngagementColor($loansCommitmentRate, 'loans')),
        ];
    }

    protected function getEngagementThresholds(string $type): array
    {
        return match ($type) {
            'comments' => [
                'low' => 10,
                'medium' => 25,
                'good' => 40,
            ],
            'ratings' => [
                'low' => 15,
                'medium' => 30,
                'good' => 45,
            ],
            'loans' => [
                'low' => 10,
                'medium' => 25,
                'good' => 40,
            ],
        };
    }

    protected function getEngagementDescription(string $type, int $rate): string
    {
        $thresholds = $this->getEngagementThresholds($type);
        
        return match (true) {
            $rate < $thresholds['low'] => 'Engagement faible',
            $rate < $thresholds['medium'] => 'Engagement moyen',
            $rate < $thresholds['good'] => 'Bon engagement',
            default => 'Excellent engagement',
        };
    }

    protected function getEngagementIcon(int $rate, string $type): string
    {
        $thresholds = $this->getEngagementThresholds($type);
        
        return match (true) {
            $rate < $thresholds['low'] => 'heroicon-m-arrow-trending-down',
            $rate < $thresholds['medium'] => 'heroicon-m-arrow-trending-up',
            $rate < $thresholds['good'] => 'heroicon-m-arrow-up-circle',
            default => 'heroicon-m-star',
        };
    }

    protected function getEngagementColor(int $rate, string $type): string
    {
        $thresholds = $this->getEngagementThresholds($type);
        
        return match (true) {
            $rate < $thresholds['low'] => 'danger',
            $rate < $thresholds['medium'] => 'warning',
            $rate < $thresholds['good'] => 'success',
            default => 'secondary',
        };
    }
}
