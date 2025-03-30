<?php

namespace App\Filament\Pages\Statistics\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Loan;
use Filament\Support\RawJs;

class BookLoanStats extends ChartWidget
{
    protected static ?int $limit = 10;
    protected static ?string $maxHeight = '300px';
    


    public function getHeading(): ?string
    {
        return 'Les ' . static::$limit . ' livres les plus empruntés';
    }

    protected function getData(): array
    {
        $topBorrowedBooks = Loan::query()
            ->select('book_id')
            ->selectRaw('COUNT(*) as loan_count')
            ->with('book:id,title,missing')
            ->groupBy('book_id')
            ->orderByDesc('loan_count')
            ->limit(static::$limit)
            ->get();

        $loanCounts = $topBorrowedBooks->pluck('loan_count')->toArray();
        $maxLoans = max($loanCounts);
        
        $colors = array_map(function ($count) use ($maxLoans) {
            $opacity = 0.4 + ($count / $maxLoans * 0.6); // Opacité entre 0.4 et 1.0
            return "rgba(99, 102, 241, {$opacity})";
        }, $loanCounts);

        return [
            'datasets' => [
                [
                    'label' => 'Nombre d\'emprunts',
                    'data' => $loanCounts,
                    'backgroundColor' => $colors,
                    'borderColor' => $colors,
                    'borderWidth' => 0,
                    'fullTitles' => $topBorrowedBooks->map(fn ($loan) => $loan->book->title)->toArray(),
                    'bookIds' => $topBorrowedBooks->pluck('book_id')->toArray(),
                ],
            ],
            'labels' => $topBorrowedBooks->map(function ($loan) {
                $words = explode(' ', $loan->book->title);
                return implode(' ', array_slice($words, 0, 5)) . (count($words) > 5 ? '...' : '');
            })->toArray(),
        ];
    }

    protected function getOptions(): RawJs
    {
        return RawJs::make(<<<JS
            {
                onClick: (e, elements) => {
                    if (elements.length > 0) {
                        const index = elements[0].index;
                        const title = e.chart.data.datasets[0].fullTitles[index];
                        window.open('/admin/books?tableSearch=' + encodeURIComponent(title), '_blank');
                    }
                },
                onHover: (e, elements) => {
                    e.native.target.style.cursor = elements.length ? 'pointer' : 'default';
                },
                scales: {
                    y: {
                        ticks: {
                            stepSize: 1,
                            precision: 0
                        }
                    }
                },
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let fullTitle = context.dataset.fullTitles[context.dataIndex];
                                return fullTitle + ': ' + context.raw + ' emprunts';
                            }
                        }
                    }
                }
            }
        JS);
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
