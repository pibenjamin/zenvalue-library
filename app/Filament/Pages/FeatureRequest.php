<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;

class FeatureRequest extends Page
{


    use HasPageShield;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationLabel   = 'Demande d\'évolution';
    protected static ?string $title             = 'Demande d\'évolution';
    protected static ?string $navigationGroup   = 'Support & Ressources';
    protected static ?int $navigationSort       = 4;
    protected static string $view = 'filament.pages.feature-request';
}
