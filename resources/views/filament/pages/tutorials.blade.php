<x-filament-panels::page>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <!-- Carte Tutoriel 1 -->



        @foreach (App\Models\Tutoriel::all() as $tutoriel)  
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden">
            <div class="relative" style="padding-bottom: 55%;">
                <iframe
                    src="{{ $tutoriel->video_url }}"
                    frameborder="0"
                    allowfullscreen
                    class="absolute top-0 left-0 w-full h-full"
                ></iframe>  
            </div>
            <div class="p-4">
                <h3 class="text-lg font-semibold mb-2">{{ $tutoriel->titre }}</h3>
                <p class="text-gray-600 dark:text-gray-300 text-sm">
                {!! nl2br(e($tutoriel->description)) !!}
                {!!html_entity_decode($tutoriel->description)!!}
                </p>
            </div>
        </div>
        @endforeach

</x-filament-panels::page>

<script>
    // Initialize Plyr for local videos only
    const players = ['player1', 'player2', 'player3'].map(id => new Plyr(`#${id}`));
</script>


