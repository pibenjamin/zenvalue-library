<?php

namespace App\Filament\Widgets;

use App\Models\Book;
use Filament\Widgets\Widget;
use Illuminate\Support\Str;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;

class BookTagCloud extends Widget
{
    use HasWidgetShield;

    protected static string $view = 'filament.widgets.book-tag-cloud';
    protected int|string|array $columnSpan = 'full';

    protected function getHeading(): ?string
    {
        return 'Nuage de mots-clés';
    }

    public function getViewData(): array
    {
        $words = collect();
        
        Book::select('title')->get()->each(function ($book) use ($words) {
            // Remplacer les apostrophes par des espaces
            $title = str_replace(['\'', '"', '’'], ' ', Str::lower($book->title));
            

            // Diviser le titre en mots
            $titleWords = preg_split('/[\s,]+/', $title);
            
            // Filtrer les mots courts et les mots vides
            $stopWords = [
                'le', 'la', 'les', 'de', 'des', 'du', 'un', 'une', 'et', 'en', 'au', 'aux', 'for', 'how', 'what', 'and',
                'l', 'd', 'j', 'n', 'm', 't', 's', 'c', 'qu', 'avec', 'the', 'pour', 'est', 'sont', 'dans', 'plus', 'ont', 'qui'
            ];
            
            $filteredWords = array_filter($titleWords, function ($word) use ($stopWords) {
                return strlen($word) > 2 && !in_array($word, $stopWords);
            });
            
            // Compter la fréquence des mots
            foreach ($filteredWords as $word) {
                $word = trim($word, '.,!?:;()[]{}"\'-');
                if (!empty($word)) {
                    $words->push($word);
                }
            }
        });

        // Calculer la fréquence
        $wordCounts = $words->countBy()->sortDesc();

        // Limiter aux 30 mots les plus fréquents
        return [
            'words' => $wordCounts->take(30)->map(function ($count, $word) use ($wordCounts) {
                $maxCount = $wordCounts->max();
                $fontSize = 12 + (($count / $maxCount) * 24);
                
                return [
                    'text' => $word,
                    'count' => $count,
                    'size' => round($fontSize, 1),
                ];
            })->values()->all()
        ];
    }
} 