<x-filament-widgets::widget>
    <x-filament::section>
        <div class="p-4">
            <span class="fi-wi-stats-overview-stat-label text-sm font-medium text-gray-500 dark:text-gray-400">Nuage de mots-clés (sujets les plus fréquents dans les titres)</span>
            <div class="flex flex-wrap gap-2">
                @foreach($words as $word)
                    <span 

                        class="inline-block px-2 py-1 rounded-full bg-primary-100 text-primary-700 hover:bg-primary-200 transition-colors cursor-default"
                        style="font-size: {{ $word['size'] }}px"
                        title="{{ $word['count'] }} occurrences"
                    >
                    <a href="/admin/books?tableSearch={{ $word['text'] }}">
                        {{ $word['text'] }}
                    </a>
                    </span>
                @endforeach
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>