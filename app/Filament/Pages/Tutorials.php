<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
class Tutorials extends Page
{
    use HasPageShield;

    protected static ?string $navigationLabel   = 'Tutoriels';
    protected static string $view               = 'filament.pages.tutorials';
    protected static ?string $navigationGroup   = 'Support & Ressources';
    protected static ?int $navigationSort       = 3;
    protected static ?string $navigationIcon    = 'heroicon-o-video-camera';
    protected static ?string $title             = 'Tutoriels vidéo';


    protected function getHeaderWidgets(): array
    {
        return [


        ];
    }

}
