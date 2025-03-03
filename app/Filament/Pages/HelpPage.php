<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Support\Facades\FilamentIcon;

class HelpPage extends Page
{
    protected static ?string $navigationLabel   = 'Aide';
    protected static ?string $title             = 'Centre d\'aide';
    protected static ?string $navigationGroup   = 'Support & Ressources';
    protected static ?int $navigationSort       = 1;
    protected static ?string $navigationIcon    = 'heroicon-o-question-mark-circle';

    
    protected static string $view = 'filament.pages.help';

    public static function shouldRegister(): bool
    {
        return true; // Vous pouvez ajouter une condition ici si nécessaire
    }

    protected function getHeaderActions(): array
    {
        return [
            // Actions dans l'en-tête si nécessaire
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            // Widgets dans l'en-tête si nécessaire
        ];
    }
} 