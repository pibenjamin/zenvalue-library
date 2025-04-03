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
                        'drop_off'      => 'Déposer au bureau',
                        'keep_at_home'  => 'Garder à la maison',
                    ])
                    ->icons([
                        'keep_at_home'  => 'heroicon-o-home',
                        'drop_off'      => 'heroicon-o-building-office',
                    ])
                    ->colors([
                        'keep_at_home'  => 'primary',
                        'drop_off'      => 'primary',
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
                    ->view('filament.forms.components.book-cal-text-helper'),
                
                    TextInput::make('cal_page')
                    ->label('Adresse de la page')
                    ->disableLabel()                   
                    ->rules(['starts_with:https://www.chasse-aux-livres.fr/prix/'])
                    ->prefixIcon('heroicon-o-globe-alt'), 
                    
                    TextInput::make('isbn')
                    ->requiredWithout('cal_page')
                    ->label('ou l\'ISBN')
//                    ->disableLabel()
                    ->helperText('L\'isbn est un code à 14 chiffres de ce type : 9782070423528 ou 978-2070423528')
                    ->unique(Book::class, 'isbn')
                    ->validationMessages([
                        'required' => 'L\'isbn est obligatoire',
                        'unique' => 'Ce livre existe déjà dans notre catalogue',
                        'required_without' => 'Un des deux champs est obligatoire',
                    ]),
                ])
            ])
            ->action(function (array $data) {
                $isbn               = str_replace('-', '', trim($data['isbn']));
                $cal_page           = $data['cal_page'];
                $selectedLocation   = $data['location'];

                $book = Book::create([
                    'title'         => '[Livre en cours d\'ajout]',
                    'isbn'          => $isbn,
                    'cal_page'      => $cal_page,
                    'status'        => Book::STATUS_TO_QUALIFY,
                    'owner_id'      => auth()->id(),
                    'support_id'    => 1,
                    'location'      => $selectedLocation,
                    'is_contribution' => true,
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