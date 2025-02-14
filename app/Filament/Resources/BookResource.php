<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BookResource\Pages;
use App\Filament\Resources\BookResource\RelationManagers;
use App\Models\Book;
use App\Models\Author;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

use Illuminate\Database\Eloquent\SoftDeletingScope;

use Filament\Infolists\Infolist;
use App\Models\Loan;
use App\Models\Tag;
use App\Models\User;
use App\Models\Notification;
use App\Services\LoanService;

use Illuminate\Contracts\View\View;

use Filament\Tables\Enums\ActionsPosition;

use Filament\Resources\Components\Tab;
use Filament\Tables\Actions\Action;
use Illuminate\Support\Facades\Http;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Radio;

use App\Filament\Imports\ProductImporter;
use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;
use Faker\Provider\ar_EG\Text;
use Filament\Actions\ImportAction;

use Filament\Tables\Filters\TextInputFilter;
use Filament\Tables\Filters\Filter;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\ActionGroup;

class BookResource extends Resource
{
    protected static ?string $model             = Book::class;
    protected static ?string $modelLabel        = 'Ouvrage';
    protected static ?string $pluralModelLabel  = 'Ouvrages';
    
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
                            ->required(),
                    ]),                    
                Forms\Components\TextInput::make('slug')
                    ->visibleOn(['edit', 'create'])
                    ->maxLength(255)
                    ->default(null),
//                Forms\Components\TextInput::make('author')
//                    ->maxLength(255)
//                    ->default(null),
                Forms\Components\FileUpload::make('cover_url')

                    ->label('Couverture')
//                    ->image()
//                    ->imageEditor()
//                    ->directory('/')
//                    ->disk('public')
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
                Forms\Components\Toggle::make('open_library_parsed')
                    ->label('Open Library')
                    ->required(),
                Forms\Components\TextInput::make('original_filename')
                    ->label('Nom du fichier original')
                    ->maxLength(255)
                    ->default(null),

                Forms\Components\Select::make('owner_id')
                    ->label('Propriétaire')
                    ->relationship('owner', 'name')
                    ->required(),

                Forms\Components\TextInput::make('pages')
                    ->label('# Pages')
                    ->numeric()
                    ->default(null),

                DatePicker::make('published_at')
                    ->label('Date de publication')
                    ->format('Y-m-d')
                    ->displayFormat('Y')
                    ->native(false)
                    ->default(null),

                Forms\Components\TextInput::make('publisher')
                    ->label('Editeur')
                    ->maxLength(255)

                    ->default(null),
                Forms\Components\TextInput::make('quantity')
                    ->required()
                    ->label('# Exemplaires')
                    ->numeric()
                    ->default(1),

                Forms\Components\Select::make('support_id')
                    ->label('Support')
                    ->relationship('support', 'name')
                    ->required(),

                Forms\Components\Select::make('theme_id')
                    ->label('Thème')
                    ->relationship('theme', 'name'),

                Forms\Components\Radio::make('difficulty_level')
                    ->label('Difficulté')
                    ->options(Book::getDifficulties())
                    ->default(null),


            ]);
    }

    public static function table(Table $table): Table
    {
        // Colonnes communes à tous les utilisateurs
        $commonColumns = [
            TextColumn::make('title')
                ->label('Titre')
                ->sortable()
                ->wrap()
                ->searchable(),

            TextColumn::make('authors.name')
                ->label('Auteurs')
                ->badge()
                ->color('gray')
                ->wrap()
                ->searchable(),

            Tables\Columns\ImageColumn::make('cover_url')
                ->label('Couverture')
                ->url(fn (Book $record): string => $record->cover_url ? $record->cover_url : url('/books/cover/book-placeholder.jpeg'))
                ->sortable()
                ->height(100),
                
            Tables\Columns\TextColumn::make('is_borrowed')
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

            TextColumn::make('published_at')
                ->date('Y')
                ->label('Année')
                ->sortable(),

            TextColumn::make('owner.name')
                ->label('Propriétaire')
                ->sortable()
                ->searchable()
                ->wrap(),

            TextColumn::make('tags.title')
                ->label('Tags')
                ->badge()
                ->color('gray')
                ->wrap()
                ->searchable(),


            TextColumn::make('difficulty_level')
                ->label('Difficulté')
                ->sortable()
                ->badge()
                ->state(function ($record): string {
                    return $record->getDifficultyLabel();
                })
                ->color(fn (Book $record): string => $record->getDifficultyColor()),

        ];

        // Colonnes supplémentaires pour les admins
        $adminColumns = [
            TextColumn::make('id')
                ->label('ID')
                ->sortable(),

                TextColumn::make('missing')
                ->label('Perdu')
                ->state(function ($record): string {
                    return $record->missing ? 'oui' : 'non';
                })
                ->badge()
                ->color(fn (string $state): string => match ($state) {
                    'oui' => 'success',
                    'non' => 'danger',
                })
                ->sortable(),

            TextColumn::make('isbn')
                ->label('ISBN')
                ->sortable(),
        ];

        // Actions communes
        $commonActions = [
            Tables\Actions\Action::make('borrow')
                ->label('Emprunter')
                ->color('success')
                ->icon('heroicon-s-hand-raised')
                ->requiresConfirmation()
                ->modalHeading('Emprunter ce livre')
                ->modalDescription(fn (Book $book) => "Voulez-vous emprunter {$book->title} ?")
                ->action(function (Book $book) {
                    app(LoanService::class)->borrowBook($book);
                })
                ->visible(fn (Book $book) => !$book->is_borrowed)
        ];

        // Actions supplémentaires pour les admins
        $adminActions = [
            ActionGroup::make([

            Tables\Actions\EditAction::make(),
            Tables\Actions\DeleteAction::make()
                ->requiresConfirmation(false),
            Tables\Actions\Action::make('open_library')
                ->label('O.L. API')
                ->icon('heroicon-o-globe-alt')
                ->modalContent(fn (Book $record): View => view(
                    'books.open-library-modal',
                    [
                        'record' => $record,
                        'bookData' => Http::get("https://openlibrary.org/isbn/{$record->isbn}.json")->json(),
                    ]
                ))
                ->modalSubmitAction(false)
                ->modalCancelAction(false)
                ->visible(fn (Book $record) => $record->isbn !== null),
            ])
        ];

        // Configuration de la table selon le rôle
        if (auth()->user()?->hasAnyRole(['super_admin', 'admin'])) {
            $table->columns(array_merge($adminColumns, $commonColumns))
                ->actions(
                        array_merge($adminActions)
                )
                ->bulkActions([
                    Tables\Actions\BulkActionGroup::make([
                        Tables\Actions\DeleteBulkAction::make(),
                    ]),
                ]);
        } else {
            $table->columns($commonColumns)
                ->defaultPaginationPageOption(50)
                ->paginationPageOptions([25, 50, 100])
                ->actions($commonActions);
        }

        $table->defaultPaginationPageOption(200)
        ->paginationPageOptions([200, 500, 1000]);

        // Filtres communs à tous
        $table->filters([
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
        ]);

        $table->actionsPosition(ActionsPosition::BeforeColumns);

        return $table;
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
