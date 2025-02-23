<?php

namespace App\Filament\Pages;

// Models
use App\Models\User;

// Filament
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\App;

// Widgets
use App\Filament\Widgets\AdminWidgets;
use App\Filament\Widgets\MyLoanHistory;
use App\Filament\Widgets\BookTagCloud;
use App\Filament\Widgets\MyBookLenders;
use App\Filament\Widgets\WhoBorrowedMyBooks;
use App\Filament\Widgets\UserStatsWidgets;


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
            WhoBorrowedMyBooks::class,

        ];
    }
}
