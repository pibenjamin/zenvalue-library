<?php

namespace App\Filament\Resources;

use Filament\Resources\Resource;
use Filament\Pages\Actions\Action;
use Illuminate\Support\Facades\Auth;

use App\Models\User;

class RoleResource extends Resource
{
    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()->hasRole(['admin', 'super_admin']);
    }




    // Si vous voulez aussi bloquer l'accès direct par URL


            // ... reste du code existant ...
} 