<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Filament\Widgets\MyLoanHistory;

use App\Filament\Widgets\StatsOverviewWidget;
use App\Filament\Widgets\UserStatsWidgets;

use BezhanSalleh\FilamentShield\Traits\HasPageShield;

class ReleaseNote extends Page
{
    use HasPageShield;

    protected static ?string $navigationLabel   = 'Notes de version';
    protected static string $view               = 'filament.pages.release-note';
    protected static ?string $navigationGroup   = 'Support & Ressources';
    protected static ?int $navigationSort       = 5;
    protected static ?string $navigationIcon    = 'heroicon-o-document-text';
    protected static ?string $title             = 'Notes de version';

    protected function getHeaderWidgets(): array
    {
        return [


        ];
    }


}
