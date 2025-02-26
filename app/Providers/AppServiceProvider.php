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

        //Gate::policy(Book::class, AdminBookPolicy::class);
    }
}