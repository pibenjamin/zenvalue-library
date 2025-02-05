<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Filament\Widgets\MyLoanHistory;

use App\Filament\Widgets\StatsOverviewWidget;

class AdminDashboard extends Page
{
    protected static ?string $navigationIcon    = 'heroicon-o-users';
    protected static ?string $navigationLabel   = 'Admin Dashboard';
    protected static string $view               = 'filament.pages.user-dashboard';




    public static function canAccess(): bool
    {
        if(in_array(auth()->user()->role->name, ['admin', 'librarian'])) 
        {
            return true;
        }
        return false;
    }

    protected function getHeaderWidgets(): array
    {
        return [
            MyLoanHistory::class
        ];
    }


}
