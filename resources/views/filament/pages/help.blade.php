<x-filament-panels::page>
    <x-filament::section>
        <div class="prose dark:prose-invert max-w-none">
            <div class="space-y-8">
                <div>
                    <h3>Gestion des livres</h3>
                    <div class="space-y-4">
                        <p>L'application permet de gérer une collection de livres avec les fonctionnalités suivantes :</p>
                        <ul>
                            <li>Ajout de livres avec ISBN, titre, auteurs, et couverture</li>
                            <li>Classification par tags</li>
                            <li>Génération de QR codes pour chaque livre</li>
                            <li>Suivi des livres manquants</li>
                            <li>Intégration avec l'API OpenLibrary pour récupérer les informations des livres</li>
                            <li>Scan de livres via appareil photo</li>
                            <li>Import depuis Amazon</li>
                        </ul>
                    </div>
                </div>

                <div>
                    <h3>Gestion des prêts</h3>
                    <div class="space-y-4">
                        <p>Le système de prêt permet de :</p>
                        <ul>
                            <li>Emprunter un livre disponible</li>
                            <li>Suivre la date de retour prévue</li>
                            <li>Signaler le retour d'un livre</li>
                            <li>Voir l'historique des prêts</li>
                            <li>Voir les statistiques de prêts par utilisateur</li>
                            <li>Recevoir des notifications par email</li>
                        </ul>
                    </div>
                </div>

                <div>
                    <h3>FAQ</h3>
                    <div class="space-y-4">
                        <details class="group">
                            <summary class="cursor-pointer font-medium">
                                Comment emprunter un livre ?
                            </summary>
                            <div class="mt-2 space-y-2">
                                <p>Pour emprunter un livre :</p>
                                <ol>
                                    <li>Accédez à la liste des livres</li>
                                    <li>Vérifiez que le livre est disponible (non emprunté)</li>
                                    <li>Cliquez sur le bouton "Emprunter"</li>
                                    <li>Confirmez votre emprunt</li>
                                </ol>
                            </div>
                        </details>

                        <details class="group">
                            <summary class="cursor-pointer font-medium">
                                Comment scanner un livre ?
                            </summary>
                            <div class="mt-2 space-y-2">
                                <p>Pour scanner un livre :</p>
                                <ol>
                                    <li>Cliquez sur "Scanner un livre"</li>
                                    <li>Prenez une photo de la couverture</li>
                                    <li>Le système tentera de reconnaître le livre</li>
                                    <li>Complétez les informations manquantes si nécessaire</li>
                                </ol>
                            </div>
                        </details>

                        <details class="group">
                            <summary class="cursor-pointer font-medium">
                                Comment fonctionne l'inscription ?
                            </summary>
                            <div class="mt-2">
                                <ol>
                                    <li>Créez un compte avec email et mot de passe sécurisé</li>
                                    <li>Un administrateur doit valider votre compte</li>
                                    <li>Vous recevrez un email une fois votre compte activé</li>
                                    <li>Vous pourrez alors vous connecter et emprunter des livres</li>
                                </ol>
                            </div>
                        </details>

                        <details class="group">
                            <summary class="cursor-pointer font-medium">
                                Comment fonctionne la recherche ?
                            </summary>
                            <div class="mt-2 space-y-2">
                                <p>Vous pouvez rechercher des livres par :</p>
                                <ul>
                                    <li>Titre</li>
                                    <li>Auteur</li>
                                    <li>Tags</li>
                                    <li>ISBN</li>
                                    <li>Propriétaire</li>
                                    <li>Statut (emprunté/disponible)</li>
                                </ul>
                                <p>Les tags et les auteurs sont cliquables pour un filtrage rapide.</p>
                            </div>
                        </details>
                    </div>
                </div>

                <div>
                    <h3>Contactez-nous</h3>
                    <div class="mt-2 text-sm">
                        <span class="text-gray-600">Une question ? </span>
                        <a href="mailto:benjaminpiscart@gmail.com" class="text-primary-600 hover:text-primary-500">
                            Contactez le support 📧
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </x-filament::section>
</x-filament-panels::page> 