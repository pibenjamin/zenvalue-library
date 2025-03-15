<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Models\Book;
use Illuminate\Support\Facades\Auth;


use App\Filament\Widgets\BookLanguageStats;

class Statistics extends Page
{
    protected static ?string $navigationLabel   = 'Statistiques';
    protected static string $view               = 'filament.pages.statistics';
    protected static ?int $navigationSort       = 5;
    protected static ?string $navigationIcon    = 'heroicon-o-presentation-chart-line';
    protected static ?string $title             = 'Statistiques';

    protected function getHeaderWidgets(): array
    {
        return [
            BookLanguageStats::class,

        ];
    }


}
