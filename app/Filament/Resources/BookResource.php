<?php

namespace App\Filament\Resources;

// Filament Resource Core
use Filament\Resources\Resource;
use App\Filament\Resources\BookResource\Pages;
use App\Filament\Resources\BookResource\RelationManagers;
use Filament\Support\Enums\Alignment;
// Models
use App\Models\Book;
use App\Models\Author;
use App\Models\Loan;
use App\Models\Tag;
use App\Models\User;
use App\Models\Support;
use App\Models\Rating;
use App\Models\Claim;
use App\Models\Comment;
use App\Models\Parcours;
use Illuminate\Support\HtmlString;
use Mokhosh\FilamentRating\Components\Rating as RatingComponent;
use Filament\Forms\Components\Checkbox;
// Services
use App\Services\LoanService;
use App\Services\QrCodeService;

// Filament Forms
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\FileUpload;

// Filament Tables
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TextInputFilter;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Enums\ActionsPosition;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint;


use Webbingbrasil\FilamentAdvancedFilter\Filters\TextFilter;
use Webbingbrasil\FilamentAdvancedFilter\Filters\BooleanFilter;

use App\Filament\Resources\BookResource\Widgets\ContributionWidget;



// Filament Other
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Support\Enums\ActionSize;
use Filament\Actions\ImportAction;

// Laravel
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Http;

// Third Party
use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;
use Faker\Provider\ar_EG\Text;
use Livewire\WithFileUploads;
use Filament\Tables\Columns\ImageColumn;
use NunoMaduro\Collision\Adapters\Phpunit\State;

// Filament Plugins
use Filament\Tables\Filters\QueryBuilder;
use Filament\Tables\Actions\BulkAction;
use App\Filament\Resources\BookResource\Pages\ListBooks;

use App\Services\BookService;




class BookResource extends Resource
{
    protected static ?string $model                 = Book::class;
    protected static ?string $modelLabel            = 'Catalogue';
    protected static ?string $pluralModelLabel      = 'Catalogue';
    protected static ?string $navigationGroup       = 'Gestion du catalogue';
    protected static ?int $navigationSort           = 1;
    protected static ?string $navigationIcon        = 'heroicon-o-book-open';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->label('Titre')
                    ->placeholder('Titre de l\'ouvrage')
                    ->helperText('Le titre de l\'ouvrage est obligatoire')
                    ->required()
                    ->maxLength(255),
                
                Forms\Components\Toggle::make('missing')
                    ->label('Manquant')
                    ->onColor('success')
                    ->offColor('danger')
                    ->default(false),

                Forms\Components\Textarea::make('description')
                    ->label('Description')
                    ->placeholder('Description de l\'ouvrage')
                    ->helperText('La description de l\'ouvrage est optionnelle'),

                Forms\Components\Select::make('authors')
                    ->label('Auteurs')
                    ->multiple()
                    ->relationship('authors', 'name')
                    ->preload()
                    ->createOptionForm([
                        Forms\Components\TextInput::make('name')
                            ->label('Nom')
                            ->required(),
                    ]),

                Forms\Components\Select::make('tags')
                    ->label('Mots-clés')
                    ->multiple()
                    ->relationship('tags', 'title')
                    ->preload()
                    ->createOptionForm([
                        Forms\Components\TextInput::make('title')
                            ->label('Nom')
                            ->placeholder('Nom du mot-clé')
                            ->unique(ignoreRecord: true)
                            ->required(),
                    ]),           

                Forms\Components\FileUpload::make('cover_url')
                    ->label('Couverture')
                    ->directory('books/covers')
                    ->maxSize(5120) // 5MB
                    ->columnSpanFull(),

                Forms\Components\TextInput::make('google_api_page')
                    ->label('Google API')
                    ->maxLength(255)
                    ->default(null)
                    ->hiddenOn(['edit', 'create']),

                Forms\Components\TextInput::make('isbn')
                    ->label('ISBN')
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\Toggle::make('is_borrowed')
                    ->label('Emprunté')
                    ->required(),

                Forms\Components\Select::make('owner_id')
                    ->label('Propriétaire')
                    ->relationship('owner', 'name')
                    ->required(),

                Forms\Components\TextInput::make('pages')
                    ->label('# Pages')
                    ->numeric()
                    ->default(null),

                Forms\Components\TextInput::make('year_of_publication')
                    ->label('Année de publication')
                    ->numeric()
                    ->minValue(1800)
                    ->maxValue(now()->year)
                    ->default(null),

                Forms\Components\TextInput::make('publisher')
                    ->label('Editeur')
                    ->maxLength(255)
                    ->default(null),

                Forms\Components\Radio::make('difficulty_level')
                    ->label('Difficulté')
                    ->options(Book::getDifficulties())
                    ->default(null),

                Forms\Components\Select::make('support_id')
                    ->label('Support')
                    ->default(Support::where('slug', 'papier')->first()->id)
                    ->relationship('support', 'name')
                    ->required(),

                Forms\Components\TextInput::make('slug')
                    ->visibleOn(['edit', 'create'])
                    ->maxLength(255)
                    ->default(null),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function (Builder $query) {
                return $query->withCount('ratings')
                    ->withCount('comments as comments_count')
                    ->whereIn('status', [Book::STATUS_ON_SHELF])
                    ->where('missing', false)
                    ->selectSub(
                        Rating::selectRaw('ROUND(AVG(rate))')
                            ->whereColumn('book_id', 'books.id')
                            ->where('user_id', auth()->id()),
                        'users_rating'
                    );
            })
            ->columns([
                TextColumn::make('title')
                    ->label('Titre')
                    ->state(function ($record): string {
                        if($record->is_borrowed) {
                            return new HtmlString($record->title . ' <span class="text-gray-500 text-sm">(Emprunté, retour prévu le ' . \Carbon\Carbon::parse($record->getLastLoan()->to_be_returned_at)->format('d/m/Y') . ')</span>');
                        }
                        return $record->title;
                    })
                    ->html()
                    ->sortable()
                    ->wrap()
                    ->searchable(),

                TextColumn::make('is_borrowed')
                    ->label('Disponibilité')
                    ->state(function (Book $record): string {
                        return $record->is_borrowed ? 'Emprunté' : 'Disponible';
                    })
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Emprunté' => 'danger',
                        'Disponible' => 'success',
                    })
                    ->tooltip(fn (Book $record) => $record->is_borrowed 
                        ? "Retour prévu le " . \Carbon\Carbon::parse($record->getLastLoan()->to_be_returned_at)->format('d/m/Y')
                        : "Ce livre est actuellement disponible"
                    )
                    ->toggleable(),

                TextColumn::make('location')
                    ->label('Localisation')
                    ->state(function (Book $record): string {
                        return Book::getLocationLabel($record->location);
                    })
                    ->badge()
                    ->color(fn (Book $record): string => Book::getLocationColor($record->location))
                    ->toggleable(),

                    
                ImageColumn::make('cover_url')
                    ->label('Couverture')
                    ->sortable()
                    ->defaultImageUrl(url('/storage/books/covers/book-placeholder.jpeg'))
                    ->height(75)
                    ->alignment(Alignment::Center)
                    ->toggleable(),

                TextColumn::make('lang')
                    ->label('Langue')
                    ->sortable()
                    ->badge()
                    ->state(function (Book $record): string {
                        return $record->lang ?? '?';
                    })
                    ->toggleable(isToggledHiddenByDefault: true),
                
                TextColumn::make('year_of_publication')
                    ->label('Année')
                    ->sortable()
                    ->toggleable(),

                ImageColumn::make('authors.photo_url')
                    ->label('Portraits')
                    ->circular()
                    ->stacked()
                    ->toggleable()
                    ->tooltip(fn (Book $record): string => $record->authors->pluck('name')->implode(', '))
                    ->height(50),

                TextColumn::make('authors.name')
                    ->label('Auteurs')
                    ->width('200px')
                    ->badge()
                    ->color('gray')
                    ->wrap()
                    ->listWithLineBreaks()
                    ->verticallyAlignStart()
                    ->searchable()
                    ->toggleable(),

                Tables\Columns\ImageColumn::make('owner.avatar')
                    ->label('Propriétaire')
                    ->circular()
                    ->defaultImageUrl(fn (Book $record): string => $record->owner->name 
                        ? url('https://ui-avatars.com/api/?name=' . 
                            implode('', array_map(fn($word) => strtoupper(substr($word, 0, 1)), 
                            explode(' ', $record->owner->name))) . 
                            '&color=FFFFFF&background=09090b') 
                        : url('/avatar/default-avatar.png'))
                    ->tooltip(fn (Book $record): string => $record->owner->name)
                    ->toggleable()
                    ->height(50),

                TextColumn::make('difficulty_level')
                    ->label('Difficulté')
                    ->sortable()
                    ->badge()
                    ->state(function ($record): string {
                        return $record->getDifficultyLabel();
                    })
                    ->toggleable()
                    ->color(fn (Book $record): string => $record->getDifficultyColor()),

                TextColumn::make('tags.title')
                    ->label('Mots-clés')
                    ->tooltip(fn (Book $record) => $record->tags->pluck('title')->implode(' - '))
                    ->url(fn (Book $record) => url('/admin/tags?tableSearch=&tableFilters[tags][id][value]=' . $record->id))
                    ->openUrlInNewTab()
                    ->listWithLineBreaks()
                    ->badge()
                    ->color('gray')
                    ->wrap()
                    ->toggleable()
                    ->searchable(),

                Tables\Columns\ViewColumn::make('rating_avg_rate')
                    ->label(new HtmlString('Note <br> moyenne'))
                    ->view('filament.tables.columns.rating_avg_rate')
                    ->tooltip("Moyenne des notes des utilisateurs")
                    ->alignment(Alignment::Center)
                    ->toggleable(),

                Tables\Columns\ViewColumn::make('users_rating')
                    ->label('Ma note')
                    ->view('filament.tables.columns.my_rate')
                    ->alignment(Alignment::Center)
                    ->toggleable(),
            ])
            ->actions([
                ActionGroup::make([
                    Tables\Actions\Action::make('borrow')
                        ->label('Emprunter')
                        ->color('success')
                        ->icon('heroicon-s-shopping-bag')
                        ->requiresConfirmation()
                        ->modalHeading('Emprunter ce livre')
                    ->modalDescription(function (Book $book){

                        if($book->location === Book::LOCATION_KEEP_AT_HOME) {

                            $html  = "Voulez-vous emprunter {$book->title} ?<br>";
                            $html .= "Ce livre est gardé chez son propriétaire {$book->owner->name}, il va être informé par courriel de votre intérêt pour ce livre !<br>";
                            $html .= "N'hésitez pas à le contacter sur teams si nécessaire.<br>";
                            $html .= "Si le livre est muni d'un QR Code vous pourrez enregistrer votre prêt via la caméra de votre télépéhone !";

                            return new HtmlString($html);
                        }

                        return "Voulez-vous emprunter {$book->title} ?";
                    })
                     ->action(function (Book $book) {
                        if($book->location === Book::LOCATION_KEEP_AT_HOME) {
                            app(BookService::class)->borrowBookAtHome($book, auth()->user());
                        }
                        else {
                            app(LoanService::class)->borrowBook($book);
                        }
                    })
                    ->tooltip(fn (Book $book) => $book->isBorrowedByUser(auth()->user()) ? 'Vous avez déjà emprunté ce livre' : 'Emprunter')
                    ->button()
                    ->size(ActionSize::Small)
                    ->visible(fn (Book $book) => !$book->is_borrowed),


                Tables\Actions\Action::make('leaveRatingAction')
                    ->label('Noter')
                    ->disableLabel()
                    ->tooltip('Noter ce livre')
                    ->button()
                    ->color('success')
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
                    ->action(function (array $data, Book $book) {
                        if(!$rating = Rating::where('book_id', $book->id)->where('user_id', auth()->id())->first()) {
                            Rating::create([
                                'book_id' => $book->id,
                                'rate' => $data['rate'],
                                'user_id' => auth()->id(),
                            ]);
                        } else {
                            Rating::where('book_id', $book->id)
                                ->where('user_id', auth()->id())
                                ->update(['rate' => $data['rate']]);
                        }
                    }),

                Tables\Actions\Action::make('comment')
                    ->label('Commenter')
                    ->disableLabel()
                    ->tooltip('Commenter ce livre')
                    ->button()
                    ->color('success')
                    ->icon('heroicon-s-chat-bubble-bottom-center-text')
                    ->modalDescription('Pour ajouter un commentaire, veuillez nous confirmer sur vous l\'avez déjà lu 🙂')
                    ->form([
                        Textarea::make('comment')
                            ->label('Commentaire')
                            ->required()
                            ->default(''),
                        Checkbox::make('Je confirme avoir lu ce livre')
                            ->label('J\'ai lu ce livre')
                            ->required()
                            ->default(false),                            
                    ])
                    ->action(function (array $data, Book $book) {
                        Comment::create([
                            'book_id' => $book->id,
                            'comment' => $data['comment'],
                            'user_id' => auth()->id(),
                        ]);
                    }),

                Tables\Actions\Action::make('tags')
                    ->label('Ajouter un mot-clé')
                    ->disableLabel()
                    ->color('success')
                    ->icon('heroicon-s-tag')
                    ->button()
                    ->modalDescription(fn (Book $record) => "Gérer les mots-clés pour le livre : {$record->title}")
                    ->form([
                        Forms\Components\Select::make('tags')
                            ->label('Mots-clés')
                            ->multiple()
                            ->relationship('tags', 'title')
                            ->preload()
                            ->default(fn (Book $record): array => $record->tags->pluck('id')->toArray())
                            ->createOptionForm([    
                                Forms\Components\TextInput::make('title')
                                    ->label('Nom')
                                    ->placeholder('Nom du mot-clé')
                                    ->unique(ignoreRecord: true)
                                    ->required(),
                            ])  
                        ])
                        ->visible(auth()->user()->hasAnyRole(['admin', 'super_admin', 'contributor'])),
                Tables\Actions\Action::make('claim')
                    ->label('Réclamer')                    
                    ->disableLabel()
                    ->color('success')
                    ->icon('heroicon-s-hand-raised')
                    ->requiresConfirmation()
                    ->modalHeading('Revendiquer la propriété de ce livre')
                     ->modalDescription(fn (Book $book) => "Voulez-vous pensez être le propriétaire de ce livre ?")
                      ->action(function (Book $book) {
                         $newClaim = new Claim();
                         $newClaim->book_id = $book->id;
                         $newClaim->user_id = auth()->id();
                         $newClaim->status = 'pending';
                         $newClaim->save();
                     })
                     ->tooltip('Revendiquer la propriété de ce livre')
                     ->visible(fn (Book $book) => $book->location === Book::LOCATION_DROP_OFF)
                     ->button(),
                     
                Tables\Actions\ViewAction::make()
                    ->label('Voir')
                    ->disableLabel()
                    ->button()
                    ->color('stone')
                    ->tooltip('Voir les détails'),
                ])
                ->label('Actions')
                ->icon('heroicon-m-ellipsis-vertical')
                ->size(ActionSize::Small)
                ->color('primary')
                ->button()
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    //Pages\ListBookAdmins::bulkAddTagsAction(),
                    BulkAction::make('print_qr_code')
                        ->label('Imprimer les QR Codes')
                        ->icon('heroicon-o-qr-code')
                        ->requiresConfirmation()
                        ->modalHeading('Imprimer les QR Codes')
                        ->modalDescription('Il vous suffira de coller cette étiquette au dos de la couverture du livre avec une colle en stick et de rappeler à l\'emprunteur d\'enregistrer le prêt.')
                        ->modalSubmitActionLabel('Oui imprimer la sélection')                        
                        //return new HtmlString('')

                        ->action(function (array $data, $livewire): void {
                            $ids            = $livewire->getSelectedTableRecords()->pluck('id')->toArray();
                            $serializedIds  = implode(',', $ids);
                            $url            = route('print-qr-codes', 
                                [
                                    'ids' => $serializedIds,
                                    'print_size' => 300,
                                    'regenerate' => true,
                                ]
                            );

                            $livewire->js("window.open('{$url}', '_blank')");
                        }),
                ])
            ])
            ->defaultPaginationPageOption(50)
            ->paginationPageOptions([25, 50, 100])
            ->filters([

                TextFilter::make('title')
                    ->label('Titre'),

                TextFilter::make('id')
                    ->label('ID'),

                Tables\Filters\SelectFilter::make('authors.name')
                    ->label('Auteurs')
                    ->multiple()
                    ->relationship('authors', 'name')
                    ->options(Author::all()->pluck('name', 'id')),

                Tables\Filters\SelectFilter::make('owner.name')
                    ->label('Propriétaire')
                    ->multiple()
                    ->relationship('owner', 'name')
                    ->options(User::all()->pluck('name', 'id')),

                Tables\Filters\SelectFilter::make('parcours.name')
                    ->label('Parcours')
                    ->multiple()
                    ->relationship('parcours', 'name')
                    ->options(Parcours::all()->pluck('name', 'id'))
                    ->preload(),


                Tables\Filters\SelectFilter::make('tags.title')
                    ->label('Mots-clés')
                    ->preload()
                    ->multiple()
                    ->query(function (Builder $query, array $data): Builder {
                        return empty($data['values']) ? $query : $query->whereHas('tags', function ($query) use ($data) {
                            $query->whereIn('id', $data['values']);
                        }, '=', count($data['values']));
                    })
                    ->relationship('tags', 'title')
                    ->options(Tag::all()->pluck('title', 'id')),

                Tables\Filters\SelectFilter::make('lang')
                    ->label('Langue')
                    ->options([
                        'fr' => 'Français',
                        'en' => 'Anglais',
                    ])
                    ->default(null),

                Tables\Filters\SelectFilter::make('difficulty_level')
                    ->label('Difficulté')
                    ->options(Book::getDifficulties())
                    ->default(null),

                BooleanFilter::make('is_borrowed')->nullsAreFalse()
                    ->label('Emprunté ?')

            ], layout: FiltersLayout::Modal)
            ->filtersFormColumns(3)
            ->actionsPosition(ActionsPosition::BeforeColumns);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('')
                    ->schema([
                        Infolists\Components\TextEntry::make('book_resource_infolist_menu')
                            ->view('filament.infolists.book_resource_menu'),
                    ])
                    ->columnSpanFull(),

                // Section Disponibilité
                Infolists\Components\Section::make('Disponibilités')
                    ->schema([
                        Infolists\Components\TextEntry::make('is_borrowed')
                            ->label('Disponibilité')
                            ->state(function (Book $record): string {
                                return $record->is_borrowed ? 'Emprunté' : 'Disponible';
                            }),
                        Infolists\Components\TextEntry::make('is_borrowed')
                            ->label('Retour prévu le')
                            ->state(function (Book $record): string {
                                if ($record->getLastLoan()) {
                                    return \Carbon\Carbon::parse($record->getLastLoan()->to_be_returned_at)->format('d/m/Y');
                                }
                                return 'Non renseigné';
                            })                            
                    ])
                    ->columns(2)
                    ->id('disponibilites')
                    ->collapsible()
                    ->icon('heroicon-o-bookmark')
                    ->iconColor('primary'),

                // Section Informations principales
                Infolists\Components\Section::make('Informations')
                    ->schema([
                        Infolists\Components\TextEntry::make('title')
                            ->label('Titre'),
                        Infolists\Components\ImageEntry::make('cover_url')
                            ->label('Couverture'),
                        Infolists\Components\TextEntry::make('authors.name')
                            ->label('Auteurs')
                            ->badge(),
                        Infolists\Components\TextEntry::make('year_of_publication')
                            ->label('Année de publication'),
                        Infolists\Components\TextEntry::make('publisher')
                            ->label('Editeur'),
                        Infolists\Components\TextEntry::make('isbn')
                            ->label('ISBN'),
                    ])
                    ->id('informations')
                    ->icon('heroicon-o-information-circle')
                    ->iconColor('primary')
                    ->collapsible()
                    ->columns(2),

                // Section Informations supplémentaires
                Infolists\Components\Section::make('Informations supplémentaires')
                    ->schema([
                        Infolists\Components\TextEntry::make('description')
                            ->label('Description')
                            ->columnSpanFull(),
                        Infolists\Components\TextEntry::make('tags.title')
                            ->label('Mots-clés')
                            ->badge()
                    ])
                    ->id('informations-supplementaires')
                    ->icon('heroicon-o-plus-circle')
                    ->iconColor('primary')
                    ->collapsible()
                    ->columns(2),

                // Section Apports des citizens
                Infolists\Components\Section::make('Apports des citizens')
                    ->schema([
                        Infolists\Components\TextEntry::make('owner.name')
                            ->label('Propriétaire')
                            ->badge(),
                        Infolists\Components\TextEntry::make('difficulty_level')
                            ->label('Difficulté')
                            ->state(function (Book $record): string {
                                return $record->getDifficultyLabel() == null ? 'Non renseigné' : $record->getDifficultyLabel();
                            })
                            ->badge(),
                        Infolists\Components\TextEntry::make('ratings.rate')
                            ->state(function (Book $record): string {
                                return $record->ratings->count() == 0 ? 'Non renseigné' : $record->ratings->avg('rate');
                            })
                            ->label('Note')
                            ->badge(),
                        Infolists\Components\TextEntry::make('comments')
                            ->label('Commentaires')
                            ->view('filament.infolists.components.comments-list')
                            ->columnSpanFull(),
                    ])
                    ->id('apports-citizens')
                    ->icon('heroicon-o-sparkles')
                    ->iconColor('primary')
                    ->collapsible()
                    ->columns(2),
            ])
            ->columns(2);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBooks::route('/'),
            'create' => Pages\CreateBook::route('/create'),
            'edit' => Pages\EditBook::route('/{record}/edit'),
            'view' => Pages\ViewBook::route('/{record}'),
        ];
    }

    public static function getWidgets(): array
    {
        return [
            ContributionWidget::class,
        ];
    }

}