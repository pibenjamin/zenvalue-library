<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

use Filament\Pages\Auth\Login;
use Filament\Pages\Auth\Register;
use Filament\Pages\Auth\EmailVerification\EmailVerificationPrompt;
use Filament\Pages\Auth\PasswordReset\RequestPasswordReset;
use Filament\Pages\Auth\PasswordReset\ResetPassword;

use Filament\Support\Enums\MaxWidth;
use Filament\Navigation\NavigationItem;
 

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
            ->login()                      // Active la page de connexion
            ->registration(Register::class) // Active l'inscription utilisateur
            ->emailVerification()          // Active la vérification d'email
            ->passwordReset()              // Active la réinitialisation de mot de passe
            ->profile()                    // Active la page de profil utilisateur

            // Personnalisation du thème
            ->colors([
                'primary' => Color::Amber, // Couleur principale du thème
            ])

            // Auto-découverte des composants Filament
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')  // Ressources (CRUD)
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')             // Pages personnalisées
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')       // Widgets du dashboard

            // Configuration des pages et widgets par défaut
            ->pages([
                Pages\Dashboard::class,    // Page dashboard par défaut
            ])
            ->widgets([
                Widgets\AccountWidget::class,                  // Widget d'informations du compte
                Widgets\FilamentInfoWidget::class,             // Informations sur Filament
                \App\Filament\Widgets\LatestBooks::class,      // Widget des derniers livres
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
            ->authMiddleware([
                Authenticate::class,                          // Vérifie que l'utilisateur est connecté
            ]);

            
    }
    
}
