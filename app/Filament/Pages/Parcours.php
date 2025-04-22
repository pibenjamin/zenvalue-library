<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class Parcours extends Page
{
    protected static ?string $navigationLabel   = 'Mon parcours';
    protected static string $view               = 'filament.pages.parcours';

    protected static ?int $navigationSort       = 2;
    protected static ?string $navigationIcon    = 'heroicon-o-map';
    protected static ?string $title             = 'Mon parcours';
    protected static ?string $slug              = 'mon-parcours';


    public static function canAccess(): bool
    {
        
        if(env('APP_ENV') !== 'local') {
            return false;
        }

        return auth()->user()->parcours()->exists();
    }

}
