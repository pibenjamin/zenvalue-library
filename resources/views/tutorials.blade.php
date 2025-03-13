<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="robots" content="noindex, nofollow">
        <meta name="googlebot" content="noindex, nofollow">
        <title>Tutoriels</title>
        <script src="https://cdn.tailwindcss.com"></script>
        <link rel="stylesheet" href="https://cdn.plyr.io/3.7.8/plyr.css" />
    </head>
    <body class="bg-gray-100 dark:bg-gray-900">
        <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
            <div class="space-y-6">
                <div class="flex items-center gap-x-3">
                    <svg class="h-6 w-6 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path d="M4.26 10.147a60.436 60.436 0 00-.491 6.347A48.627 48.627 0 0112 20.904a48.627 48.627 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.57 50.57 0 00-2.658-.813A59.905 59.905 0 0112 3.493a59.902 59.902 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.697 50.697 0 0112 13.489a50.702 50.702 0 017.74-3.342M6.75 15a.75.75 0 100-1.5.75.75 0 000 1.5zm0 0v-3.675A55.378 55.378 0 0112 8.443m-7.007 11.55A5.981 5.981 0 006.75 15.75v-1.5" />
                    </svg>
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
                        Tutoriels
                    </h2>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <!-- Carte Tutoriel 1 -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden">
                        <div class="plyr__video-embed" id="player1">
                            <video 
                                controls
                                class="w-full h-full"
                            >
                                <source src="/storage/video/activate-existing-acount.mp4" type="video/mp4">
                            </video>
                        </div>
                        <div class="p-4">
                            <h3 class="text-lg font-semibold mb-2">Activer un compte citizen existant</h3>
                            <p class="text-gray-600 dark:text-gray-300 text-sm">
                                Si vous avez rejoint avant le <span class="text-red-500 font-semibold">15 février 2025</span>, suivez ce guide pour activer votre compte.
                            </p>
                        </div>
                    </div>

                    <!-- Carte Tutoriel 2 -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden">
                        <div class="plyr__video-embed" id="player2">
                            <video 
                                controls
                                class="w-full h-full"
                            >
                                <source src="/storage/video/create-new-citizen-account.mp4" type="video/mp4">
                            </video>
                        </div>
                        <div class="p-4">
                            <h3 class="text-lg font-semibold mb-2">Créer un nouveau compte citizen</h3>
                            <p class="text-gray-600 dark:text-gray-300 text-sm">
                                Si vous avez rejoint après le <span class="text-red-500 font-semibold">15 février 2025</span>, suivez ce guide pour créer votre compte.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script src="https://cdn.plyr.io/3.7.8/plyr.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const players = Plyr.setup('video');
            });
        </script>
    </body>
</html>