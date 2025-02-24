<?php

namespace App\Filament\Resources\BookResource\Pages;

use App\Filament\Resources\BookResource;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

use App\Models\Rating;
use Mokhosh\FilamentRating\Components\Rating as RatingComponent;

class ListBooks extends ListRecords
{
    protected static string $resource = BookResource::class;

    public function leaveRatingAction(): Action
    {
        return Action::make('leaveRatingAction')
            ->link()
            ->icon('heroicon-o-hand-thumb-up')
            ->form([
                RatingComponent::make('rate')
                    ->default(5)
                    ->required()
            ])
            ->action(function (array $data, array $arguments) {
                Rating::create([
                    'book_id' => $arguments['book'],
                    'rate' => $data['rate'],
                    'user_id' => auth()->id(),
                ]);
            });
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            __('Tous les livres') => Tab::make(),
            __('Mes livres') => Tab::make()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('owner_id', auth()->user()->id)),
        ];
    }
}
