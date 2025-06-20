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
use Filament\Forms\Components\ToggleButtons;
use App\Models\Rating;
use App\Models\Book;
use Mokhosh\FilamentRating\Components\Rating as RatingComponent;
use Filament\Notifications\Notification;
use Illuminate\Support\HtmlString;
use Closure;
use App\Filament\Resources\BookResource\Widgets\ContributionWidget;
use Filament\Forms\Components\Wizard\Step;
use Filament\Forms\Components\Html;
use Filament\Forms\Components\ViewField;
use Filament\Forms;
use App\Services\ImportBookData;
use Filament\Support\Colors\Color;

class ListBooks extends ListRecords
{
    protected static string $resource = BookResource::class;

    public function triggerContributeAction(): Action
    {
        return $this->getContributeAction();    
    }

    protected function getContributeAction(): Action 
    {

        https://www.chasse-aux-livres.fr/prix/2749962412/les-gueux-on-fait-quoi-alexandre-jardin

        return Action::make('contribute')
        ->label('Ajouter un de mes livres au catalogue')
        ->size('xl')
        ->icon('heroicon-o-plus')
        ->color('primary')
        ->steps([
            Step::make('Localisation')
                ->description('Choisissez la localisation du livre')
                ->schema([
                    ToggleButtons::make('location')
                    ->disableLabel()
                    ->options([
                        Book::LOCATION_DROP_OFF             => 'Déposer au bureau',
                        Book::LOCATION_KEEP_AT_HOME         => 'Garder à la maison',
                    ])
                    ->icons([
                        Book::LOCATION_DROP_OFF             => 'heroicon-o-building-office',
                        Book::LOCATION_KEEP_AT_HOME         => 'heroicon-o-home',
                    ])
                    ->colors([
                        Book::LOCATION_DROP_OFF             => 'primary',
                        Book::LOCATION_KEEP_AT_HOME         => 'primary',
                    ])
                    ->inline()
                    ->extraAttributes([
                        'class' => 'fi-size-2xl',
                    ])
                    ->grouped()
                    ->default(null)
                    ->required()
                    ->validationMessages([
                        'required' => 'Veuillez préciser où sera stocké le livre',
                    ]),

                    ViewField::make('rating')
                    ->view('filament.forms.components.book-location-text-helper'),
                    ]),
            Step::make('Informations indispendables')
                ->description('Merci de renseigner ses informations')
                ->schema([  
                    ViewField::make('rating')
                    ->view('filament.forms.components.book-isbn-text-helper'),
                
                    TextInput::make('isbn')
                    ->label('ISBN')
                    ->placeholder('XXXXXXXXXXXXX')
                    ->disableLabel()                   
                    ->prefixIcon('heroicon-o-book-open')
                    ->required(),
                ]),
            Step::make('QR code')
                ->description('Ajouter un QR code à votre livre')
                ->schema([  
                    ViewField::make('qr_code')
                    ->view('filament.forms.components.book-qr-code-helper'),

                    Checkbox::make('qr_code_interest')
                    ->label('Je suis intéressé.e par le QR code')
                    ->default(false),
                ])
            
        ])
        ->action(function (array $data) {
            $cal_page           = $data['cal_page'];
            $selectedLocation   = $data['location'];
            $qr_code_interest   = $data['qr_code_interest'];

            $book = Book::create([
                'title'             => '[Livre en cours d\'ajout]',
                'cal_page'          => $cal_page,
                'status'            => Book::STATUS_TO_QUALIFY,
                'owner_id'          => auth()->id(),
                'support_id'        => 1,
                'location'          => $selectedLocation,
                'is_contribution'   => true,
                'qr_code_interest'  => $qr_code_interest,
            ]);

            Notification::make()
                ->title('Livre en cours d\'ajout au catalogue')
                ->success()
                ->send();
        });
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
        return [
            Actions\CreateAction::make(),
            $this->getContributeAction(),
        ];
    }

    public function getHeaderWidgets(): array
    {

        if (auth()->user()->hasRole('user')) {

            if(Book::where('owner_id', auth()->id())
            ->where('status', Book::STATUS_TO_QUALIFY)
            ->where('location', 'drop_off')
            ->count() > 0) {
                return [
                    ContributionWidget::class,
                ];
            }
        }

        return [];
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