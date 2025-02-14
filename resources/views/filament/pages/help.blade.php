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
                            <li>Classification par thèmes et tags</li>
                            <li>Indication du niveau de difficulté (Facile, Moyen, Difficile, Expert)</li>
                            <li>Suivi des livres manquants</li>
                            <li>Intégration avec l'API OpenLibrary pour récupérer les informations des livres</li>
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
                            <li>Gérer les retards</li>
                            <li>Valider les retours (administrateurs uniquement)</li>
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
                                    <li>Vérifiez que le livre est disponible (badge vert)</li>
                                    <li>Cliquez sur le bouton "Emprunter"</li>
                                    <li>Confirmez votre emprunt</li>
                                </ol>
                            </div>
                        </details>

                        <details class="group">
                            <summary class="cursor-pointer font-medium">
                                Comment rendre un livre ?
                            </summary>
                            <div class="mt-2 space-y-2">
                                <p>Pour rendre un livre :</p>
                                <ol>
                                    <li>Accédez à "Mes prêts"</li>
                                    <li>Trouvez le livre à rendre</li>
                                    <li>Cliquez sur "Rendre ce livre"</li>
                                    <li>Un administrateur validera le retour</li>
                                </ol>
                            </div>
                        </details>

                        <details class="group">
                            <summary class="cursor-pointer font-medium">
                                Que signifient les niveaux de difficulté ?
                            </summary>
                            <div class="mt-2">
                                <ul>
                                    <li><strong>Facile :</strong> Lecture accessible à tous</li>
                                    <li><strong>Moyen :</strong> Nécessite des connaissances de base</li>
                                    <li><strong>Difficile :</strong> Pour lecteurs expérimentés</li>
                                    <li><strong>Expert :</strong> Niveau avancé/technique</li>
                                </ul>
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
                                </ul>
                                <p>Les tags sont cliquables et permettent un filtrage rapide de la collection.</p>
                            </div>
                        </details>
                    </div>
                </div>
            </div>
        </div>
    </x-filament::section>
</x-filament-panels::page> 