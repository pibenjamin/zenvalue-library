<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Filament\Widgets\MyLoanHistory;

use App\Filament\Widgets\StatsOverviewWidget;
use App\Filament\Widgets\UserStatsWidgets;
use App\Filament\Widgets\BookTagCloud;
use App\Filament\Widgets\AdminWidgets;

class OkrPage extends Page
{
    protected static ?string $navigationLabel   = 'OKR';
    protected static string $view               = 'filament.pages.okr';
    protected static ?string $navigationGroup   = 'Support & Ressources';
    protected static ?int $navigationSort       = 2;
    protected static ?string $navigationIcon    = 'heroicon-o-chart-bar';
    protected static ?string $title             = 'Objectifs et résultats clés';

    protected function getHeaderWidgets(): array
    {
        return [


        ];
    }


}
