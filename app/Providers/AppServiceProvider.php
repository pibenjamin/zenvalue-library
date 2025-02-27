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
        ]);



        Str::macro('slugify', function ($string) {

            // replace french special characters by their ascii equivalent
            $string = str_replace('é', 'e', $string);
            $string = str_replace('è', 'e', $string);
            $string = str_replace('ê', 'e', $string);
            $string = str_replace('à', 'a', $string);
            $string = str_replace('ù', 'u', $string);
            $string = str_replace('ç', 'c', $string);
            $string = str_replace('î', 'i', $string);
            $string = str_replace('ô', 'o', $string);
            $string = str_replace('û', 'u', $string);
            $string = str_replace('œ', 'oe', $string);
            $string = str_replace('ï', 'i', $string);
            
            
            return Str::slug(preg_replace('/[^\w\d]+/', '-', $string), '-', 'fr');    
    
        });

        //Gate::policy(Book::class, AdminBookPolicy::class);
    }
}