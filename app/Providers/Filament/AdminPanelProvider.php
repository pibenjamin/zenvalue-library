<?php

namespace App\Providers\Filament;

// Filament Core
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\MaxWidth;
use Filament\Support\Assets\Asset;
use Filament\Support\Assets\Js;

// Filament Navigation
use Filament\Navigation\NavigationItem;
use Filament\Navigation\NavigationGroup;
use Filament\Navigation\MenuItem;
use Filament\Navigation\NavigationBuilder;

// Filament Pages
use Filament\Pages;
use App\Filament\Pages\OkrPage;
use App\Filament\Pages\CustomLogin;

// Filament Auth Pages
use Filament\Pages\Auth\Login;
use Filament\Pages\Auth\Register;
use Filament\Pages\Auth\EmailVerification\EmailVerificationPrompt;
use Filament\Pages\Auth\PasswordReset\RequestPasswordReset;
use Filament\Pages\Auth\PasswordReset\ResetPassword;
use App\Filament\Pages\Auth\EditProfile;
use App\Livewire\Auth\CustomRegister;
use App\Livewire\Auth\CustomResetPassword;

// Filament Widgets
use Filament\Widgets;
use App\Filament\Widgets\LatestBooksAdded;
use App\Filament\Widgets\LatestBooksPublished;
use App\Filament\Widgets\AdminWidgets;
use App\Filament\Widgets\MyLoanHistory;
use App\Filament\Widgets\BookTagCloud;
use App\Filament\Widgets\MyBookLenders;
use App\Filament\Widgets\WhoBorrowedMyBooks;
use App\Filament\Widgets\UserStatsWidgets;
use App\Filament\Widgets\LatestBooksAddedWidgets;
use App\Filament\Widgets\WhoIBorrowedFrom;
use App\Filament\Widgets\Borrowers;
use App\Filament\Widgets\EmployeesOverview;
use App\Filament\Widgets\CommitmentWidgets;
use App\Filament\Widgets\AddBookActionWidget;

// Filament Resources
use App\Filament\Resources\RoleResource;

// Filament Plugins
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;

// Laravel Middleware
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

// Laravel Support
use Illuminate\Support\Facades\App;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            // Configuration de base du panel
            ->default()                    // Définit ce panel comme le panel par défaut
            ->id('admin')                  // Identifiant unique du panel
            ->path('admin')                // URL d'accès : example.com/admin
            // Configuration de l'authentification
            ->login(CustomLogin::class)                      // Active la page de connexion
            ->registration(CustomRegister::class) // Active l'inscription utilisateur
            ->emailVerification()          // Active la vérification d'email
            ->passwordReset(RequestPasswordReset::class, CustomResetPassword::class)              // Active la réinitialisation de mot de passe
            ->profile(EditProfile::class)                    // Active la page de profil utilisateur
            
            // Personnalisation du thème
            ->colors([
                'primary' => Color::Amber, // Couleur principale du thème
                'secondary' => Color::Sky, // Couleur secondaire du thème
            ])

            ->userMenuItems([
//                MenuItem::make()
//                    ->label('Mes statistiques')
//                    ->icon('heroicon-o-chart-bar')
//                    ->url(fn (): string => \App\Filament\Resources\UserResource::getUrl('index'))
            ])

            ->navigationGroups([
                NavigationGroup::make()
                    ->label('Gestion du catalogue'),
                NavigationGroup::make()
                    ->label('Gestion des prêts'),
                NavigationGroup::make()
                    ->label('Gestion des utilisateurs'),
                NavigationGroup::make()
                    ->label('Gestion des rôles'),
                NavigationGroup::make()
                    ->label('Support & Ressources'),
                    // Ici seront rangés : work, aide et contact
            ])

            // Auto-découverte des composants Filament
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')  // Ressources (CRUD)
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')             // Pages personnalisées
//            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')       // Widgets du dashboard

            // Configuration des pages et widgets par défaut
            ->pages([
                Pages\Dashboard::class,    // Page dashboard par défaut
            ])
            ->widgets([
                LatestBooksAddedWidgets::class,
                LatestBooksPublished::class,
                MyLoanHistory::class,
                CommitmentWidgets::class,
                AdminWidgets::class,
                UserStatsWidgets::class,
                BookTagCloud::class,
                Borrowers::class,
                //AddBookActionWidget::class,
                //BookLanguageStats::class,
                //EmployeesOverview::class,
                //WhoIBorrowedFrom::class,
            ])
            // Middleware de sécurité et fonctionnalités
            ->middleware([
                EncryptCookies::class,                        // Chiffrement des cookies
                AddQueuedCookiesToResponse::class,            // Gestion des cookies en file
                StartSession::class,                          // Démarrage de la session
                AuthenticateSession::class,                   // Authentication de la session
                ShareErrorsFromSession::class,                // Partage des erreurs de validation
                VerifyCsrfToken::class,                      // Protection contre les attaques CSRF
                SubstituteBindings::class,                    // Injection de dépendances route
                DisableBladeIconComponents::class,            // Désactive les icônes Blade par défaut
                DispatchServingFilamentEvent::class,          // Events Filament
            ])

            ->plugins([
                \BezhanSalleh\FilamentShield\FilamentShieldPlugin::make()
                ->gridColumns([
                    'default' => 1,
                    'sm' => 2,
                    'lg' => 3
                ])
                ->sectionColumnSpan(1)
                ->checkboxListColumns([
                    'default' => 1,
                    'sm' => 2,
                    'lg' => 4,
                ])
                ->resourceCheckboxListColumns([
                    'default' => 1,
                    'sm' => 2,
                ]),
                

            ])
            ->authMiddleware([
                Authenticate::class,                          // Vérifie que l'utilisateur est connecté
            ])
            ->renderHook(
                'panels::head.end',
                fn () => '<script async defer src="https://teams.microsoft.com/share/launcher.js"></script>'
            );
    }
}