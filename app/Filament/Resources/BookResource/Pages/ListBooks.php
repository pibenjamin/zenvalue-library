<?php

namespace App\Filament\Resources\BookResource\Pages;

use App\Filament\Resources\BookResource;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\TextInput;
use App\Models\Rating;
use App\Models\Book;
use Mokhosh\FilamentRating\Components\Rating as RatingComponent;
use Filament\Notifications\Notification;

class ListBooks extends ListRecords
{
    protected static string $resource = BookResource::class;

    public function leaveRatingAction(): Action
    {
        return Action::make('leaveRatingAction')
            ->label('Noter ce livre')
            ->link()
            ->modalDescription('Pour noter ce livre, nous vous demandons de nous confirmer sur vous l\'avez déjà lu 🙂')
            ->icon('heroicon-o-hand-thumb-up')
            ->form([
                RatingComponent::make('rate')
                    ->label('')
                    ->allowZero()
                    ->default(0)
                    ->required(),
                Checkbox::make('Je confirme avoir lu ce livre')
                    ->label('J\'ai lu ce livre')
                    ->default(false),
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
            Actions\Action::make('contribute')
                ->label('Ajouter un de mes livres au catalogue')
                ->icon('heroicon-o-plus')
                ->color('primary')
                ->tooltip('Ajouter un de mes livres au catalogue')
                ->modalHeading('Ajouter un livre')
                ->modalDescription('Merci de renseigner le numéro ISBN de votre livre, il se trouve généralement au dos du livre en dessous du code barre.<br> Merci de déposer le livre dans la zone de la bibliothèque "Retour et nouveautés"')
                ->form([
                    TextInput::make('isbn')
                        ->label('ISBN')
                        ->required()
                        ->helperText('L\'isbn est un code à 14 chiffres de ce type : 9782070423528 ou 978-2070423528')
                        ->unique(Book::class, 'isbn')
                        ->validationMessages([
                            'required' => 'L\'isbn est obligatoire',
                            'unique' => 'Ce livre existe déjà dans notre catalogue',
                        ]),
                ])
                ->action(function (array $data) {
                    $isbn = str_replace('-', '', trim($data['isbn']));
                    
                    $book = Book::create([
                        'isbn'          => $isbn,
                        'status'        => Book::STATUS_CONTRIBUTION_TO_QUALIFY,
                        'owner_id'      => auth()->id(),
                        'support_id'    => 1,
                        'is_contribution' => true,
                    ]);

                    Notification::make()
                        ->title('Livre en cours d\'ajout au catalogue')
                        ->success()
                        ->send();
                }),
        ];
    }

    public function getTabs(): array
    {
        return [
            __('Tous les livres') => Tab::make()
            ->badge(fn () => Book::where('status', Book::STATUS_ON_SHELF)->count()),

            __('Mes livres') => Tab::make()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('owner_id', auth()->id()))
                ->badge(fn () => Book::where('owner_id', auth()->id())->count()),
        ];
    }
}