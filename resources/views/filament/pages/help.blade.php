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
                            <li>Qualification avec des mots clés, la difficulté, l'avis des citizens et la note moyenne</li>
                            <li>Génération de QR codes pour chaque livre</li>
                            <li>Suivi des livres manquants</li>
                            <li>Intégration avec l'API OpenLibrary pour récupérer des informations des livres</li>
                            <li>Emprunt simplifié avec scan du QR code du livre</li>
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
                            <li>Voir l'historique de mes emprunts</li>
                            <li>Recevoir des notifications de rappel de livres à retourner (par email)</li>
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
                                    <li>Ouvrez l'appareil photo de votre téléphone</li>
                                    <li>Scannez le QR code du livre</li>
                                    <li>Connectez-vous si vous n'êtes pas déjà identifié</li>
                                    <li>Cliquez sur le bouton "Emprunter"</li>
                                    <li>OU</li>
                                    <li>Faites une recherche dans le catalogue</li>
                                    <li>Choisissez un livre et cliquez sur "Emprunter"</li>
                                </ol>
                            </div>
                        </details>

                        <details class="group">
                            <summary class="cursor-pointer font-medium">
                                Comment fonctionne l'inscription ?
                            </summary>
                            <div class="mt-2">
                                <ol>
                                    <li>Explication en video sur cette page : <a target="_blank" href="/admin/tutorials">{{config('app.url')}}/admin/tutorials</a>
                                </ol>
                            </div>
                        </details>
                    </div>
                </div>

                <div>
                    <h3>Contactez-nous</h3>

                    
                    <div class="mt-2 text-sm">
                        <span class="text-gray-600">Une question ? </span>


                        <a href="https://teams.microsoft.com/l/team/19%3AJh751xpc_rmPRGlvE2R-h9ROybT_kW5RPE9ZnBgfKKk1%40thread.tacv2/conversations?groupId=6ad48e47-0b9c-4b45-9b96-997289f0ab00&tenantId=12dc3f70-b08c-4adf-be76-24cc248684be" class="text-primary-600 hover:text-primary-500">
                            Contactez-nous sur Teams 💬
                        </a>

                        <br>


                        <a href="mailto:benjaminpiscart@gmail.com" class="text-primary-600 hover:text-primary-500">
                            Ou par email 📧
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </x-filament::section>
</x-filament-panels::page> 