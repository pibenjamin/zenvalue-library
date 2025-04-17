<x-filament-panels::page>
    <x-filament::section
        icon="heroicon-o-map-pin"
        {{--aside--}}
        collapsible
        persist-collapsed
        id="statistics-section"
        >
        <x-slot name="heading">
            Parcours actif
        </x-slot>
        <div>
            @livewire(\App\Filament\Pages\Parcours\Widgets\CurrentParcoursWidget::class)
        </div>
    </x-filament::section>

    <x-filament::section
        icon="heroicon-o-chart-bar-square"
        {{--aside--}}
        collapsible
        persist-collapsed
        id="statistics-section"
        >
        <x-slot name="heading">
            Progression dans le parcours
        </x-slot>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                @livewire(\App\Filament\Pages\Parcours\Widgets\ProgressBookReadingWidget::class)
            </div>

            <div>
                @livewire(\App\Filament\Pages\Parcours\Widgets\ProgressBooksFinishedWidget::class)
            </div>
        </div>
    </x-filament::section>
</x-filament-panels::page>