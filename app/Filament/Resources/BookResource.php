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
use App\Models\Notification;
use App\Models\Rating;

use Illuminate\Support\Str;
use Illuminate\Support\HtmlString;
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
                    ->label('Tags')
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
                ->where('status',Book::STATUS_ON_SHELF)
                ->where('missing', false)
                    ->selectSub(function ($query) {
                        $query->from('ratings')
                            ->selectRaw('ROUND(AVG(rate))')
                            ->whereColumn('book_id', 'books.id')
                            ->where('user_id', auth()->id());
                    }, 'users_rating');
            })
            ->columns([
                TextColumn::make('title')
                    ->label('Titre')
                    ->sortable()
                    ->wrap()
                    ->searchable(),
                    
                ImageColumn::make('cover_url')
                    ->label('Couverture')
                    ->sortable()
                    ->defaultImageUrl(url('/storage/books/covers/book-placeholder.jpeg'))
                    ->height(75)
                    ->alignment(Alignment::Center),

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
                    ->sortable(),

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
                    ->searchable(),

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
                    ->alignment(Alignment::Center),

                Tables\Columns\ViewColumn::make('users_rating')
                    ->label('Ma note')
                    ->view('filament.tables.columns.my_rate')
                    ->tooltip("Ma note personnelle")
                    ->alignment(Alignment::Center),

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
                    ),

            ])
            ->actions([
                Tables\Actions\Action::make('borrow')
                    ->label('Emprunter')
                    ->color('success')
                    ->icon('heroicon-s-shopping-bag')
                    ->requiresConfirmation()
                    ->modalHeading('Emprunter ce livre')
                    ->modalDescription(fn (Book $book) => "Voulez-vous emprunter {$book->title} ?")
                     ->action(function (Book $book) {
                        app(LoanService::class)->borrowBook($book);
                    })
                    ->tooltip(fn (Book $book) => $book->isBorrowedByUser(auth()->user()) ? 'Vous avez déjà emprunté ce livre' : 'Emprunter')
                    ->button()
                    ->visible(fn (Book $book) => !$book->is_borrowed),

                 Tables\Actions\Action::make('already_borrowed')
                     ->label(fn (Book $book) => 'Retour le ' . \Carbon\Carbon::parse($book->getLastLoan()->to_be_returned_at)->format('d/m/Y'))
                     ->disabled(fn (Book $book) => $book->is_borrowed)
                     ->visible(fn (Book $book) => $book->is_borrowed),

                Tables\Actions\ViewAction::make()
                    ->label('Voir')
                    ->disableLabel()
                    ->button()
                    ->iconSize('sm')
                    ->color('stone')
                    ->tooltip('Voir les détails'),
            ])
            ->defaultPaginationPageOption(50)
            ->paginationPageOptions([25, 50, 100])
            ->filters([

                TextFilter::make('title')
                    ->label('Titre'),


                Tables\Filters\SelectFilter::make('authors.name')
                    ->label('Auteurs')
                    ->multiple()
                    ->relationship('authors', 'name')
                    ->options(Author::all()->pluck('name', 'id')),


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
                    ->default(BooleanFilter::CLAUSE_IS_FALSE)
                    
                                     

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
                            ->badge(),
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
        ];
    }

}