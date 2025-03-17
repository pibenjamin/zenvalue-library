<?php

namespace App\Filament\Pages\Statistics\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Book;
use Filament\Support\RawJs;

class BookLanguageStats extends ChartWidget
{
    protected static ?string $heading = 'Répartition des livres par langue';
    protected static ?string $maxHeight = '300px';

    
    protected function getOptions(): RawJs
    {
        return RawJs::make(<<<JS
        {
            scales: {
                y: {
                    ticks: {
                        callback: (value) => value + '%',
                    },
                    display: false,
                },
                x: {
                    display: false,
                },
            },
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let count = context.dataset.bookCount[context.dataIndex];
                            return context.label + ': ' + context.raw + '% (' + count + ' livres)';
                        }
                    }
                }
            }
        }
    JS);
    }


    public function getDescription(): ?string
    {
        return 'Pourcentage et nombre de livres par langue';
    }

    protected function getData(): array
    {
        $books = Book::whereIn('status', [Book::STATUS_ON_SHELF, Book::STATUS_BORROWED])
                ->where('missing', false)->get();
        $booksLanguages = $books->groupBy('lang')->toArray();
        $totalBooks = $books->count();

        $booksByLanguage = [];
        $bookCount = [];
        foreach ($booksLanguages as $language => $books) {
            $count = count($books);
            $booksByLanguage[] = round(($count / $totalBooks) * 100, 1);
            $bookCount[] = $count;
        }

        $labels = array_keys($booksLanguages);
        $data = array_values($booksByLanguage);

        $langLabels = [
            'fr' => 'Français',
            'en' => 'Anglais',
            '?'  => '?',
            ''   => '?',
        ];

        $labels = array_map(function ($label) use ($langLabels) {
            return $langLabels[$label] ?? $label;
        }, $labels);

        return [
            'datasets' => [
                [
                    'label' => 'Nombre de livres par langue',
                    'data' => $data,
                    'bookCount' => $bookCount,
                    'backgroundColor' => [
                        'rgba(255, 99, 132, 1)',
                        'rgba(54, 162, 235, 1)',
                        'rgba(153, 102, 255, 1)',
                        'rgba(75, 192, 192, 1)',
                    ],
                ],
            ],
            'labels' => $labels,
        ];
    }


    protected function getType(): string
    {
        return 'pie';
    }
}
