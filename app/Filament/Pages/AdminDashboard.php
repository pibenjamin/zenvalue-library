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
use App\Filament\Widgets\MyBookLenders;
use Illuminate\Support\Facades\App;
use App\Filament\Widgets\MyBookBorrowers;
class AdminDashboard extends Page
{
    protected static ?string $navigationIcon        = 'heroicon-o-users';
    protected static ?string $navigationLabel       = 'Tableau de bord';
    protected static string $view                   = 'filament.pages.dashboard';
    protected static ?string $title                 = 'Tableau de bord';

    protected function getHeaderWidgets(): array
    {
        return [
            MyLoanHistory::class,
            MyBookLenders::class,
            AdminWidgets::class,
            UserStatsWidgets::class,
            BookTagCloud::class,
            MyBookBorrowers::class,

        ];
    }
}
