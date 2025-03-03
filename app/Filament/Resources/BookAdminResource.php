<?php

namespace App\Filament\Resources;

// Filament Resource Core
use Filament\Resources\Resource;
use App\Filament\Resources\BookAdminResource\Pages;
use App\Filament\Resources\BookAdminResource\RelationManagers;

// Models
use App\Models\Book;
use App\Models\BookAdmin;
use App\Models\Author;
use App\Models\User;
use App\Models\Tag;
use App\Models\Support;
// Services
use App\Services\LoanService;
use App\Services\QrCodeService;

// Filament Forms
use Filament\Forms;
use Filament\Forms\Form;

// Filament Tables
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\TextInputFilter;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Enums\ActionsPosition;


// Laravel
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Http;
use Illuminate\View\View;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Notification;
use Filament\Tables\Actions\BulkAction;
use Illuminate\Support\Str;
use Closure;    
use Filament\Icons\Icon;


class BookAdminResource extends Resource
{
    protected static ?string $model                 = Book::class;
    protected static ?string $modelLabel            = 'Catalogue (admin)';
    protected static ?string $pluralModelLabel      = 'Catalogue (admin)';
    protected static ?string $navigationGroup       = 'Gestion du catalogue';
    protected static ?string $navigationIcon        = 'heroicon-o-book-open';

    public static function canAccess(): bool
    {
        return auth()->user()->hasAnyRole(['admin', 'super_admin']);    
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informations essentielles du livre')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->label('Titre')
                            ->placeholder('Titre de l\'ouvrage')
                            ->required()
                            ->maxLength(255)
                            ->reactive()
                            ->afterStateUpdated(function (Forms\Set $set, $state) {
                                $set('slug', Str::slugify($state));
                            })
                            ->columnSpan(4),

                        Forms\Components\TextInput::make('slug')
                            ->visibleOn(['edit', 'create'])
                            ->maxLength(255)
                            ->columnSpan(4),

                        Forms\Components\Select::make('authors')
                            ->label('Auteurs')
                            ->multiple()
                            ->relationship('authors', 'name')
                            ->preload()
                            ->createOptionForm([
                                Forms\Components\TextInput::make('name')
                                    ->label('Nom')
                                    ->required(),
                            ])
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('isbn')
                            ->label('ISBN')
                            ->maxLength(255)
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

                        Forms\Components\TextInput::make('pages')
                            ->label('# Pages')
                            ->numeric()
                            ->default(null),

                        Forms\Components\TextInput::make('ol_key')
                            ->label('Open Library Key')
                            ->maxLength(255)
                            ->default(null),

                        Forms\Components\TextInput::make('lang')
                            ->label('Langue'),

                        Forms\Components\FileUpload::make('cover_url')
                            ->label('Couverture')
                            ->maxSize(5120) // 5MB
                            ->columnSpanFull()
                            ->columnSpan(4),

                    ])
                    ->columns(3)
                    ->collapsible(),

                Forms\Components\Section::make('Qualifications supplémentaires')
                    ->schema([
                        Forms\Components\Textarea::make('description')
                            ->label('Description')
                            ->placeholder('Description de l\'ouvrage')
                            ->helperText('La description de l\'ouvrage est optionnelle')
                            ->rows(5)
                            ->columnSpanFull()
                            ->columnSpan(4),


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
                            ])
                            ->columnSpan(1),
                        Forms\Components\Select::make('owner_id')
                            ->label('Propriétaire')
                            ->relationship('owner', 'name')
                            ->required()
                            ->columnSpan(1),

                        Forms\Components\Select::make('support_id')
                            ->label('Support')
                            ->default(Support::where('slug', 'papier')->first()->id)
                            ->relationship('support', 'name')
                            ->required()
                            ->columnSpan(1),

                        Forms\Components\Select::make('difficulty_level')
                            ->label('Difficulté')
                            ->options(Book::getDifficulties())
                            ->default(null)
                            ->columnSpan(1),
                    ])
                    ->columns(4)
                    ->collapsible(),


                Forms\Components\Section::make('Statut')
                    ->schema([
                        Forms\Components\Toggle::make('missing')
                            ->label('Manquant')
                            ->onColor('success')
                            ->offColor('danger')
                            ->default(false),
       
                        Forms\Components\Toggle::make('is_borrowed')
                            ->label('Emprunté')
                            ->helperText(fn(Book $record): string => $record->is_borrowed ? "jusqu'au " . \Carbon\Carbon::parse($record->getLastLoan()->to_be_returned_at)->format('d/m/Y') : 'Ce livre est actuellement disponible')
                            ->required(),
                    ])
                    ->columns(4)
                    ->collapsible(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),

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

                TextColumn::make('missing')
                    ->label('Perdu')
                    ->state(function ($record): string {
                        return $record->missing ? 'oui' : 'non';
                    })
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'oui' => 'danger',
                        'non' => 'success',
                    })
                    ->sortable(),

                TextColumn::make('isbn')
                    ->label('ISBN')
                    ->sortable(),

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
                ActionGroup::make([
                    Tables\Actions\EditAction::make(),

                    Tables\Actions\DeleteAction::make()
                        ->requiresConfirmation(false),

                    Action::make('qrcode')
                        ->label('QR Code')
                        ->icon('heroicon-o-qr-code')
                        ->action(fn (Book $record) => $record)   
                        ->modalContent(fn (Book $record): View => view(
                            'filament.modals.view.qrcode',
                            ['record' => $record, 'qrCode' => app(QrCodeService::class)->generateQrCode($record, 300)],
                        ))
                        ->modalSubmitAction(false),

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
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    //Pages\ListBookAdmins::bulkAddTagsAction(),
                ]),

            ])
            ->defaultPaginationPageOption(200)
            ->paginationPageOptions([200, 500, 1000])
            ->filters([
                Tables\Filters\SelectFilter::make('id')
                    ->label('ID')
                    ->options(Book::all()->pluck('id', 'id')),

                Tables\Filters\SelectFilter::make('is_borrowed')
                    ->label('Emprunté')
                    ->options([
                        'true' => 'Oui',
                        'false' => 'Non',
                    ]),

                Filter::make('isbn')
                    ->form([
                        Forms\Components\TextInput::make('isbn')
                            ->label('ISBN')
                            
                            ->placeholder('Rechercher par ISBN'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['isbn'],
                            fn (Builder $query, $isbn): Builder => $query->where('isbn', 'like', "%{$isbn}%")
                                ->orWhereNull('isbn')
                        );
                    }),

                Filter::make('isbn_null')
                    ->form([
                        Forms\Components\Checkbox::make('isbn_null')
                            ->label('ISBN vide'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['isbn_null'],
                            fn (Builder $query): Builder => $query->whereNull('isbn')
                        );
                    }),

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
            'index' => Pages\ListBookAdmins::route('/'),

            'create' => Pages\CreateBookAdmin::route('/create'),
            'edit' => Pages\EditBookAdmin::route('/{record}/edit'),
        ];
    }
}
