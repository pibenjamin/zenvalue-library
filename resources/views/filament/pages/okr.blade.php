<x-filament::page>
    
    <p>Cette petite application a été conçue avec amour dans le but que vous puissiez connaitre facilement les livres à disposition dans notre bibliothèque.</p>
    <p>Les fonctionnalités sont limitées pour l'instant, mais si vous montrez de l'intérêt et que les KPI d'usage sont bons, j'ouvrirai je soumettais l'ajout de fonctionnalités au vote.</p>

    <p>Voici les fonctionnalités fraîchement ajoutées, en cours de développement et à venir:</p>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Fonctionnalités sociales -->
        <div class="rounded-lg border border-gray-200 bg-white p-6 dark:border-gray-700 dark:bg-gray-800">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4 flex items-center gap-x-2">
                <x-heroicon-m-user-group class="w-5 h-5 text-primary-500"/>
                Fonctionnalités sociales
            </h3>
            <ul class="space-y-3">
                <li class="flex items-center justify-between text-gray-600 dark:text-gray-300">
                    <div class="flex items-center gap-x-2">
                        <x-heroicon-m-user-group class="w-4 h-4 text-gray-400"/>
                        Qui a lu mes livres ?
                    </div>
                    <x-filament::badge color="expert">beta</x-filament::badge>
                </li>
                <li class="flex items-center justify-between text-gray-600 dark:text-gray-300">
                    <div class="flex items-center gap-x-2">
                        <x-heroicon-m-users class="w-4 h-4 text-gray-400"/>
                        Qui a lu les mêmes livres ?
                    </div>
                    <x-filament::badge color="expert">beta</x-filament::badge>
                </li>
                <li class="flex items-center justify-between text-gray-600 dark:text-gray-300">
                    <div class="flex items-center gap-x-2">
                        <x-heroicon-m-shopping-cart class="w-4 h-4 text-gray-400"/>
                        Demande d'achat de livres
                    </div>
                    <x-filament::badge color="expert">beta</x-filament::badge>
                </li>
            </ul>
        </div>

        <!-- Fonctionnalités pédagogiques -->
        <div class="rounded-lg border border-gray-200 bg-white p-6 dark:border-gray-700 dark:bg-gray-800">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4 flex items-center gap-x-2">
                <x-heroicon-m-academic-cap class="w-5 h-5 text-primary-500"/>
                Fonctionnalités pédagogiques
            </h3>
            <ul class="space-y-3">
                <li class="flex items-center justify-between text-gray-600 dark:text-gray-300">
                    <div class="flex items-center gap-x-2">
                        <x-heroicon-m-academic-cap class="w-4 h-4 text-gray-400"/>
                        Matching formations/livres
                    </div>
                    <x-filament::badge color="primary">todo</x-filament::badge>
                </li>
                <li class="flex items-center justify-between text-gray-600 dark:text-gray-300">
                    <div class="flex items-center gap-x-2">
                        <x-heroicon-m-book-open class="w-4 h-4 text-gray-400"/>
                        Livres recommandés par formation
                    </div>
                    <x-filament::badge color="primary">todo</x-filament::badge>
                </li>
                <li class="flex items-center justify-between text-gray-600 dark:text-gray-300">
                    <div class="flex items-center gap-x-2">
                        <x-heroicon-m-chat-bubble-left-right class="w-4 h-4 text-gray-400"/>
                        Commentaires sur les livres
                    </div>
                    <x-filament::badge color="primary">todo</x-filament::badge>
                </li>
            </ul>
        </div>

        <!-- Gestion des emprunts -->
        <div class="rounded-lg border border-gray-200 bg-white p-6 dark:border-gray-700 dark:bg-gray-800">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4 flex items-center gap-x-2">
                <x-heroicon-m-calendar class="w-5 h-5 text-primary-500"/>
                Gestion des emprunts
            </h3>
            <ul class="space-y-3">
                <li class="flex items-center justify-between text-gray-600 dark:text-gray-300">
                    <div class="flex items-center gap-x-2">
                        <x-heroicon-m-calendar class="w-4 h-4 text-gray-400"/>
                        Gestion des réservations
                    </div>
                    <x-filament::badge color="success">disponible</x-filament::badge>
                </li>
                <li class="flex items-center justify-between text-gray-600 dark:text-gray-300">
                    <div class="flex items-center gap-x-2">
                        <x-heroicon-m-arrow-path class="w-4 h-4 text-gray-400"/>
                        Gestion des retours
                    </div>
                    <x-filament::badge color="success">disponible</x-filament::badge>
                </li>
                <li class="flex items-center justify-between text-gray-600 dark:text-gray-300">
                    <div class="flex items-center gap-x-2">
                        <x-heroicon-m-qr-code class="w-4 h-4 text-gray-400"/>
                        Emprunts via scan d'un qr-code
                    </div>
                    <x-filament::badge color="success">disponible</x-filament::badge>
                </li>
            </ul>
        </div>
    </div>

    <p>Bonne lecture !</p>
    <p>Benjamin</p>


    <h1 class="fi-header-heading text-2xl font-bold tracking-tight text-gray-950 dark:text-white sm:text-3xl">OKR 1 : Réorganiser la bibliothèque physique pour améliorer l'accessibilité des livres</h1>
    <h2 class="fi-header-heading text-xl font-bold tracking-tight text-gray-950 dark:text-white sm:text-2xl">Objectif : Optimiser l'organisation de la bibliothèque pour faciliter l'accès aux ouvrages par les consultants.</h2>
    <ul>
        <li>Résultat Clé 1 : Classer  dans des rangements accéssibles 100 % des livres selon une catégorisation thématique pertinente d'ici la fin courant mars 2025.</li>
        <li>Résultat Clé 2 : Étiqueter tous les livres avec un code unique et une signalétique claire d'ici la fin mars 2025.</li>
        <li>Résultat Clé 3 : Mettre en place un système d'emprunt en autonomie pour les consultants d'ici la fin mars 2025.</li>
    </ul>

    <h1 class="fi-header-heading text-2xl font-bold tracking-tight text-gray-950 dark:text-white sm:text-3xl">OKR 2 : Développer une application web intuitive pour la gestion de la bibliothèque</h1>
    <h2 class="fi-header-heading text-xl font-bold tracking-tight text-gray-950 dark:text-white sm:text-2xl">Objectif : Créer une application web conviviale permettant aux consultants de gérer efficacement les emprunts et retours de livres.</h2>
    <ul>
        <li>Résultat Clé 1 : Importer 100% des ouvrages dans l'outil</li>
        <li>Résultat Clé 2 : Qualité des données : 100% des ouvrages ont un titre, un auteur, une date de publication, une ISBN ; 90% ont une couverture</li>
        <li>Résultat Clé 3 : Développer les fonctionnalités principales de l'application, y compris la recherche de livres, la gestion des emprunts et des retours, et la consultation des disponibilités, d'ici la fin du trimestre.</li>
        <li>Résultat Clé 4 : Ouvrir a tous le monde et beta tester plus finement avec 3 citizens volontaires</li>
        <li>Résultat Clé 5 : Faire un bilan en sens syncro sur l'usage, taux d'utilisation parmis les citizens</li>
    </ul>

    <h1 class="fi-header-heading text-2xl font-bold tracking-tight text-gray-950 dark:text-white sm:text-3xl">OKR 3: Développer une bibliothèque de qualité pour les consultants</h1>
    <h2 class="fi-header-heading text-xl font-bold tracking-tight text-gray-950 dark:text-white sm:text-2xl">Objectif : Avoir des titres qui adressent les besoins des consultants juniors et seniors, avec les titres de références.</h2>
    <ul>
        <li>Résultat Clé 1 : Ajouter des commentaires sur les livres et noter les niveaux de difficulté (débutant, confirmé, expert)</li>
        <li>Résultat Clé 2 : Développer les fonctionnalités principales de l'application, y compris la recherche de livres, la gestion des emprunts et des retours, et la consultation des disponibilités, d'ici la fin du trimestre.</li>
        <li>Résultat Clé 3 : Ouvrir a tous le monde et beta tester plus finement avec 3 citizens volontaires</li>
        <li>Résultat Clé 4 : Faire un bilan le 01-04-2025 sur l'usage, taux d'utilisation parmis les citizens</li>
    </ul>

</x-filament::page>
