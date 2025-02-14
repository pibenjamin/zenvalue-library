<x-filament-widgets::widget>
    <x-filament::section>
        <div class="p-4">
            <span class="fi-wi-stats-overview-stat-label text-sm font-medium text-gray-500 dark:text-gray-400 block mb-4">Nuage de mots-clés (sujets les plus fréquents dans les titres)</span>
            <div class="flex flex-wrap gap-2">
                @foreach($words as $word)
                    @php
                        $size = match(true) {
                            $word['size'] >= 24 => 'text-lg px-4 py-2',
                            $word['size'] >= 18 => 'text-base px-3 py-1.5',
                            default => 'text-sm px-2 py-1',
                        };
                    @endphp
                    <span 
                        class="inline-flex items-center gap-x-1.5 rounded-full ring-1 ring-inset ring-gray-200 text-gray-900 hover:ring-gray-300 dark:ring-gray-700 dark:text-gray-200 dark:hover:ring-gray-600 {{ $size }}"
                        title="{{ $word['count'] }} occurrences"
                    >
                        <a href="/admin/books?tableSearch={{ $word['text'] }}" class="hover:text-primary-600">
                            {{ $word['text'] }}
                        </a>
                        <span class="text-xs text-gray-500 dark:text-gray-400">({{ $word['count'] }})</span>
                    </span>
                @endforeach
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>