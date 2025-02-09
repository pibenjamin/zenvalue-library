<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Filament\Widgets\MyLoanHistory;

use App\Filament\Widgets\StatsOverviewWidget;
use App\Filament\Widgets\UserStatsWidgets;

class AdminDashboard extends Page
{
    protected static ?string $navigationIcon    = 'heroicon-o-users';
    protected static ?string $navigationLabel   = 'Tableau de bord';
    protected static string $view               = 'filament.pages.dashboard';
    protected static ?string $title               = 'Tableau de bord';


    protected function getHeaderWidgets(): array
    {
        return [
            MyLoanHistory::class,
            UserStatsWidgets::class
        ];
    }


}
