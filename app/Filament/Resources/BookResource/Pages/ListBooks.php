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
use Illuminate\Support\HtmlString;
use Closure;
class ListBooks extends ListRecords
{
    protected static string $resource = BookResource::class;

    public $defaultAction = 'addBookAction';

    public function defaultAction(): Actions\Action
    {
        return Action::make('addBookAction')
            ->label('Ajouter un livre')
            ->modalHeading('Ajouter un livre')
            ->icon('heroicon-o-plus');
    }

    public function leaveRatingAction(): Action
    {
        return Action::make('leaveRatingAction')
            ->label('Noter')
            ->disableLabel()
            ->tooltip('Noter ce livre')
            ->link()
            ->modalDescription('Pour noter ce livre, nous vous demandons de nous confirmer sur vous l\'avez déjà lu 🙂')
            ->icon('heroicon-o-star')
            ->form([
                RatingComponent::make('rate')
                    ->label('')
                    ->allowZero()
                    ->default(0)
                    ->required(),
                Checkbox::make('Je confirme avoir lu ce livre')
                    ->label('J\'ai lu ce livre')
                    ->required()
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
        $image = asset('images/poster-livre-a-qualifier.png');
        $html = <<<HTML
        <div>
            <p>Merci de renseigner le numéro ISBN de votre livre, il se 
            trouve généralement au dos du livre en dessous du code barre.</p>
            <p>Vous pourrez ensuite déposer votre livre dans le bureau, dans l'espace "livres à qualifier" sous ce poster :</p>
            <div class="text-center">
                <img src="$image" alt="Poster livres à qualifier" 
                     style="height: 100px; border: 1px solid black; margin: 10px auto;"
                >
            </div>
            <p>Notre équipe confirmera l'ajout de votre livre au catalogue et vous pourrez suivre les emprunts via l'application.</p>
            <p>Merci pour votre contribution à la bibliothèque !</p>
        </div>
        HTML;

        return [
            Actions\CreateAction::make(),
            Actions\Action::make('contribute')
                ->label('Ajouter un de mes livres au catalogue')
                ->icon('heroicon-o-plus')
                ->color('primary')
                ->tooltip('Ajouter un de mes livres au catalogue')
                ->modalHeading('Ajouter un livre')
                ->modalDescription(fn (): HtmlString => new HtmlString($html))
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
                        'status'        => Book::STATUS_TO_QUALIFY,
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
            ->badge(fn () => Book::where('status', Book::STATUS_ON_SHELF)->where('missing', false)->count()),

            __('Mes livres') => Tab::make()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('owner_id', auth()->id()))
                ->badge(fn () => Book::where('owner_id', auth()->id())->where('status', Book::STATUS_ON_SHELF)->count()),
        ];
    }
}