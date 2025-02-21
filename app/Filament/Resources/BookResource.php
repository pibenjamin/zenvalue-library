<?php

namespace App\Filament\Resources;

// Filament Resource Core
use Filament\Resources\Resource;
use App\Filament\Resources\BookResource\Pages;
use App\Filament\Resources\BookResource\RelationManagers;

// Models
use App\Models\Book;
use App\Models\Author;
use App\Models\Loan;
use App\Models\Tag;
use App\Models\User;
use App\Models\Support;
use App\Models\Notification;

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

// Filament Other
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

class BookResource extends Resource
{
    protected static ?string $model             = Book::class;
    protected static ?string $modelLabel        = 'Livre';
    protected static ?string $pluralModelLabel  = 'Livres';
    protected static ?string $navigationGroup   = 'Gestion des livres';
    
    protected static ?string $navigationIcon    = 'heroicon-o-book-open';

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
            ->columns([
                TextColumn::make('title')
                    ->label('Titre')
                    ->sortable()
                    ->wrap()
                    ->searchable(),

                ImageColumn::make('authors.photo_url')
                    ->label('Portraits')
                    ->circular()
                    ->stacked()
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
                
                ImageColumn::make('cover_url')
                    ->label('Couverture')
                    ->url(fn (Book $record): string => $record->cover_url ? $record->cover_url : url('/books/cover/book-placeholder.jpeg'))
                    ->sortable()
                    ->defaultImageUrl(url('/storage/book-placeholder.jpeg'))
                    ->height(75),
                
                TextColumn::make('is_borrowed')
                    ->label('Disponibilité')
                    ->state(function ($record): string {
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

                TextColumn::make('year_of_publication')
                    ->label('Année')
                    ->sortable(),

                TextColumn::make('owner.name')
                    ->label('Propriétaire')
                    ->badge()
                    ->color('gray')
                    ->sortable()
                    ->searchable()
                    ->wrap(),

                TextColumn::make('difficulty_level')
                    ->label('Difficulté')
                    ->sortable()
                    ->badge()
                    ->state(function ($record): string {
                        return $record->getDifficultyLabel();
                    })
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
                    ->searchable(),
            ])
            ->actions([
                Tables\Actions\Action::make('borrow')
                    ->label(fn (Book $book) => $book->isBorrowedByUser(auth()->user()) ? 'Vous empruntez ce livre' : 'Emprunter')
                    ->color('success')
                    ->icon('heroicon-s-hand-raised')
                    ->requiresConfirmation()
                    ->modalHeading('Emprunter ce livre')
                    ->modalDescription(fn (Book $book) => "Voulez-vous emprunter {$book->title} ?")
                    ->action(function (Book $book) {
                        app(LoanService::class)->borrowBook($book);
                    })
                    ->tooltip(fn (Book $book) => $book->isBorrowedByUser(auth()->user()) ? 'Vous avez déjà emprunté ce livre' : 'Emprunter')
                    ->button()
                    ->visible(fn (Book $book) => !$book->is_borrowed),
            ])
            ->defaultPaginationPageOption(50)
            ->paginationPageOptions([25, 50, 100])
            ->filters([
                Tables\Filters\SelectFilter::make('is_borrowed')
                    ->label('Emprunté')
                    ->options([
                        'true' => 'Oui',
                        'false' => 'Non',
                    ]),

                Tables\Filters\SelectFilter::make('authors.name')
                    ->label('Auteurs')
                    ->relationship('authors', 'name')
                    ->options(Author::all()->pluck('name', 'id')),

                Tables\Filters\SelectFilter::make('owner.name')
                    ->label('Propriétaire')
                    ->relationship('owner', 'name')
                    ->options(User::all()->pluck('name', 'id')),

                Tables\Filters\SelectFilter::make('tags.title')
                    ->label('Mots-clés')
                    ->multiple()
                    ->relationship('tags', 'title')
                    ->options(Tag::all()->pluck('title', 'id')),

                Tables\Filters\SelectFilter::make('difficulty_level')
                    ->label('Difficulté')
                    ->options(Book::getDifficulties())
                    ->default(null),

                Tables\Filters\SelectFilter::make('is_borrowed')
                    ->label('Disponibilité')
                    ->options([
                        'true' => 'Disponible',
                        'false' => 'Emprunté',
                    ]),
            ])
            ->actionsPosition(ActionsPosition::BeforeColumns);
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