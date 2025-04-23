<x-filament-panels::page>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <!-- Carte Tutoriel 1 -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden">
            <div class="plyr__video-embed" id="player">
                <video 
                    id="player"
                    controls
                    class="w-full h-full"
                >
                    <source src="/storage/video/activate-existing-acount.mp4" type="video/mp4">
                </video>
            </div>
            <div class="p-4">
                <h3 class="text-lg font-semibold mb-2">Activer un compte citizen déjà existant</h3>
                <p class="text-gray-600 dark:text-gray-300 text-sm">
                    Si vous avez rejoins Zen Value avant le <span class="inline-block w-fit"><x-filament::badge color="danger">15 février 2025</x-filament::badge></span>, vous pouvez activer votre compte citizen en suivant les étapes ci-dessous.
                </p>
            </div>
        </div>
        <!-- Carte Tutoriel 2 -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden">
            <div class="plyr__video-embed" id="player">
                <video 
                    id="player"
                    controls
                    class="w-full h-full"
                >
                    <source src="/storage/video/create-new-citizen-account.mp4" type="video/mp4">
                </video>
            </div>
            <div class="p-4">
                <h3 class="text-lg font-semibold mb-2">Créer un nouveau compte citizen</h3>
                <p class="text-gray-600 dark:text-gray-300 text-sm">
                    Si vous avez rejoins Zen Value après le <span class="inline-block w-fit"><x-filament::badge color="danger">15 février 2025</x-filament::badge></span>, vous pouvez créer un nouveau compte citizen en suivant les étapes ci-dessous.
                </p>
            </div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden">
            <div class="plyr__video-embed" id="player">
                <video 
                    id="player"
                    controls
                    class="w-full h-full"
                >
                    <source src="/storage/video/print-qr-codes-for-my-books.mp4" type="video/mp4">
                </video>
            </div>
            <div class="p-4">
                <h3 class="text-lg font-semibold mb-2">Imprimer le QR code de mes livres</h3>
                <p class="text-gray-600 dark:text-gray-300 text-sm">
                    Vous pouvez imprimer le QR code de vos livres en suivant les étapes ci-dessous.
                    - Selectionnez les livres grâce à l'action de masse
                    - Cliquez sur l'action "Imprimer le QR code"
                    - Imprimez le QR code et placez le dans vos livres
                </p>
            </div>
        </div>

        <!-- Carte Tutoriel 1 -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden">
            <div class="plyr__video-embed" id="player">
                <video 
                    id="player"
                    controls
                    class="w-full h-full"
                >
                    <source src="/storage/video/Emprunter-prolonger-un-pret-signaler-un-retour-de-pret.mp4" type="video/mp4">
                </video>
            </div>
            <div class="p-4">
                <h3 class="text-lg font-semibold mb-2">Emprunter - prolonger un prêt - signaler un retour de prêt</h3>
                <p class="text-gray-600 dark:text-gray-300 text-sm">
                    
                </p>
            </div>
        </div>

    </div>
</x-filament-panels::page>

<script>
    const player = new Plyr('#player');
</script>


