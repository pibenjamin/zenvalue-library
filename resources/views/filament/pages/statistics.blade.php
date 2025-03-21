<x-filament-panels::page>
<x-filament::section
    icon="heroicon-o-chart-bar"
    {{--aside--}}
    collapsible
    persist-collapsed
    id="statistics-section"
    >
        <x-slot name="heading">
            Statistiques d'usage
        </x-slot>
        <x-slot name="description">
            Voir les statistiques d'utilisation de l'application
        </x-slot>

        <div>
            @livewire(\App\Filament\Pages\Statistics\Widgets\LoansChart::class)
        </div>
        <div>
            @livewire(\App\Filament\Pages\Statistics\Widgets\BookLoanStats::class)
        </div>

        <x-slot name="headerEnd">
        {{-- Input to select the user's ID --}}
        
        </x-slot>

    </x-filament::section>
    <x-filament::section
    icon="heroicon-o-chart-bar"
    {{--aside--}}
    collapsible
    persist-collapsed
    id="statistics-section"
    >
        <x-slot name="heading">
            Données qualitatives du catalogue
        </x-slot>
        <x-slot name="description">
        </x-slot>

        <div>
            @livewire(\App\Filament\Pages\Statistics\Widgets\BookLanguageStats::class)
        </div>


        <x-slot name="headerEnd">
        {{-- Input to select the user's ID --}}
        
        </x-slot>

    </x-filament::section>
</x-filament-panels::page>
