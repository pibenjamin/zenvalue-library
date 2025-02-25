@php
    $averageRating = $getRecord()->getAverageRoundedRating();
@endphp

@if($averageRating > 0 && $averageRating <= 1)   
    <x-filament::badge color="danger">
        {{ $averageRating }}
    </x-filament::badge>
@elseif($averageRating > 1 && $averageRating <= 2)
    <x-filament::badge color="warning">
        {{ $averageRating }}
    </x-filament::badge>
@elseif($averageRating > 2 && $averageRating <= 3)
    <x-filament::badge color="info">
        {{ $averageRating }}
    </x-filament::badge>
@elseif($averageRating > 3 && $averageRating <= 4)
    <x-filament::badge color="success">
        {{ $averageRating }}
    </x-filament::badge>
@elseif($averageRating > 4 && $averageRating <= 5)
    <x-filament::badge color="primary">
        {{ $averageRating }}
    </x-filament::badge>
@endif  