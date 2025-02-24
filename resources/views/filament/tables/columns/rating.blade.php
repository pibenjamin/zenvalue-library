@php
    $state = $getState();
    $user = auth()->user();
@endphp

<div class="flex">  
    @if(blank($state))
        {{ ($this->leaveRatingAction)(['book' => $getRecord()->id]) }}
    @elseif(!$user->hasRated($getRecord()))
        {{ ($this->leaveRatingAction)(['book' => $getRecord()->id]) }}
    @else
        @for($i = 1; $i < 6; $i++)
            <div 
                @class([
                    'text-slate-300' => $state < $i,
                    'text-primary-500' => $state >= $i,
                ])
            >
                <x-heroicon-o-star class="w-6 h-6 pointer-events-none" />
            </div>
        @endfor
    @endif  
</div>
