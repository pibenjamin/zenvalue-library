<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Filament\Facades\Filament;
use Illuminate\Validation\Rules\Password;

use Filament\Support\Colors\Color;
use Filament\Support\Facades\FilamentColor;

use App\Models\Book;
use App\Policies\AdminBookPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {



        FilamentColor::register([
            'expert' => Color::Purple,
            'stone' => Color::Stone,
        ]);

        Str::macro('slugify', function ($string) {
            $replacements = [
                // Voyelles accentuées
                'é|è|ê|ë' => 'e',
                'à|â|ä' => 'a',
                'ù|û|ü' => 'u',
                'î|ï' => 'i',
                'ô|ö' => 'o',
                
                // Consonnes spéciales
                'ç' => 'c',
                'ñ' => 'n',
                
                // Ligatures
                'œ|æ' => 'oe',
                'Œ|Æ' => 'OE',
                
                // Caractères spéciaux
                '«|»|„|"|"' => '',
                '\'|\'' => '-',
                '\s+' => '-',
            ];

            // Application des remplacements avec expression régulière
            $string = preg_replace(
                array_map(fn($pattern) => "/$pattern/u", array_keys($replacements)),
                array_values($replacements),
                $string
            );
            
            return Str::slug(preg_replace('/[^\w\d]+/', '-', $string), '-', 'fr');
        });

        //Gate::policy(Book::class, AdminBookPolicy::class);
    }
}