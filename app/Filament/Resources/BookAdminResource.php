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

use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Field;

use Livewire\Component;


// Services
use App\Services\LoanService;
use App\Services\QrCodeService;
use App\Services\OpenLibraryService;
use App\Notifications\BookAddedToCatalogue;

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
use Webbingbrasil\FilamentCopyActions\Tables\CopyableTextColumn;
use Webbingbrasil\FilamentCopyActions\Forms\Actions\CopyAction;
// Laravel
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope; 
use Illuminate\Support\Facades\Http;
use Illuminate\View\View;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Mail;

use Filament\Tables\Actions\BulkAction;
use Illuminate\Support\Str;
use Closure;    
use Filament\Icons\Icon;
use Filament\Notifications\Notification;
use Illuminate\Support\HtmlString;
use Filament\Forms\Set;
use Filament\Forms\Get;
use App\Services\ImportBookData;

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

    public static function getNavigationBadge(): ?string
    {
        $booksToQualify = Book::where('status', Book::STATUS_TO_QUALIFY)->count();
        if($booksToQualify > 0){
            return $booksToQualify;
        }
        return null;
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return 'Nombre de livres à qualifier';
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
                            ->maxLength(255)
                            ->reactive()
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (Forms\Set $set, $state) {
                                $set('slug', Str::slugify($state));
                            })          
                            ->suffixAction(CopyAction::make())                  
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
                                Forms\Components\FileUpload::make('photo_url')
                                    ->label('Photo')
                                    ->directory('authors')
                                    ->maxSize(5120) // 5MB
                                    ->columnSpanFull(),
                            ])
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('isbn')
                            ->label('ISBN')
                            ->maxLength(255)
                            ->default(null),

                        Forms\Components\TextInput::make('lang')
                            ->label('Langue')
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

                        Forms\Components\FileUpload::make('cover_url')
                            ->label('Couverture')
                            ->directory('books/covers')
                            ->maxSize(5120) // 5MB
                            ->columnSpanFull()
                            ->columnSpan(4),

                    ])
                    ->columns(3)
                    ->collapsible(),

                Forms\Components\Section::make('Import de données')
                    ->schema([
                        Forms\Components\TextInput::make('ol_key')
                            ->label('Code Open Library')
                            ->helperText(function ($record) {
                                if (!$record) return null;
                                return new HtmlString(
                                    '<a href="https://openlibrary.org/works/' . $record->ol_key . '" 
                                        target="_blank" 
                                        class="text-success-600 hover:text-success-500 hover:underline"
                                    >
                                        Voir la page sur openlibrary.org
                                    </a>'
                                );
                            })
                            ->prefixIcon('heroicon-o-globe-alt')
                            ->suffixAction(
                                Forms\Components\Actions\Action::make('importFromCalPage')
                                    ->label('Importer les informations')
                                    ->icon('heroicon-o-arrow-down-tray')
                                    ->modalContent(fn (Book $record): View => view(
                                        'filament.modals.view.compare_with_ol',
                                        ['record' => $record,
                                         'ol_data' => app(OpenLibraryService::class)->extractBookDataFromOLKey($record->ol_key)],
                                    ))
                                    ->modalCancelActionLabel('Fermer')
                                    ->action(function (?Book $record) {
                                }),
                            ),

                        Forms\Components\TextInput::make('cal_page')
                            ->label('Page sur chasse aux livres')
                            ->helperText(function ($record) {
                                if (!$record) return null;
                                return new HtmlString(
                                    '<a href="https://www.chasse-aux-livres.fr" 
                                        target="_blank" 
                                        class="text-success-600 hover:text-success-500 hover:underline"
                                    >
                                        Voir la page sur www.chasse-aux-livres.fr
                                    </a>'
                                );
                            })
                            ->prefixIcon('heroicon-o-globe-alt')
                            ->suffixAction(
                                Forms\Components\Actions\Action::make('importFromCalPage')
                                    ->label('Importer les informations')
                                    ->icon('heroicon-o-arrow-down-tray')
                                    ->disabled(fn (?Book $record): bool => !$record || $record->cal_page === 'parsed')
                                    ->action(function (?Book $record) {
                                        if (!$record) return;
                                        app(ImportBookData::class)->importFromCalPage($record);
                                    }),
                            ),
                    ])
                    ->columns(2)
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

                        Forms\Components\Select::make('status')
                            ->label('Statut')
                            ->options(Book::getStatusLabels())
                            ->default(Book::STATUS_QUALIFIED),

                        Forms\Components\Select::make('location')
                            ->label('Localisation')
                            ->options(Book::getLocations())
                            ->default(Book::LOCATION_DROP_OFF),
       
                        Forms\Components\Toggle::make('is_borrowed')
                            ->label('Emprunté')
                            ->helperText(fn(?Book $record): string => 
                            !$record ? 'Ce livre sera disponible une fois créé' :
                            ($record->is_borrowed ? 
                                "jusqu'au " . \Carbon\Carbon::parse($record->getLastLoan()->to_be_returned_at)->format('d/m/Y') : 
                                'Ce livre est actuellement disponible'
                            )
                        )
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

                TextColumn::make('missing')
                    ->label('Manquant')
                    ->sortable()
                    ->badge()
                    ->state(function (Book $record): string {
                        return $record->missing ? 'oui' : 'non';
                    })
                    ->toggleable(isToggledHiddenByDefault: true),

                    
                    TextColumn::make('lang')
                    ->label('Langue')
                    ->sortable()
                    ->badge()
                    ->state(function (Book $record): string {
                        return $record->lang ?? '?';
                    })
                    ->toggleable(isToggledHiddenByDefault: true),



                TextColumn::make('cal_page')
                    ->label('Page c.a.l.')
                    ->toggleable(isToggledHiddenByDefault: true),

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
                    ->sortable()
                    ->defaultImageUrl(url('/storage/books/covers/book-placeholder.jpeg'))
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

                TextColumn::make('status')
                    ->label('Statut')
                    ->sortable()
                    ->badge()
                    ->state(function (Book $record): string {
                        return $record->getStatusLabel();
                    })
                    ->color(fn (Book $record): string => $record->getStatusColor()),

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

                    Tables\Actions\Action::make('put_on_shelf')
                        ->label('Mettre sur étagère')
                        ->icon('heroicon-o-check-circle')
                        ->action(function (Book $record) {

                            $record->status = Book::STATUS_ON_SHELF;
                            $record->save();

                            $record->owner->notify(new BookAddedToCatalogue($record));

                            Notification::make()
                                ->title('Livre ajouté au catalogue')
                                ->success()
                                ->send();
                        })
                        
                        ->modalSubmitAction(true)
                        ->modalCancelAction(false)
                        ->visible(fn (Book $record) => $record->isbn !== null),
                ])
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    //Pages\ListBookAdmins::bulkAddTagsAction(),
                    BulkAction::make('put_on_shelf')
                        ->label('Mettre sur étagère')
                        ->icon('heroicon-o-archive-box-arrow-down')
                        ->requiresConfirmation()
                        ->action(fn (Collection $records) => $records->each->putOnShelf())
                        ->modalDescription('Voulez-vous vraiment mettre le statut de ces livres à qualifier ?'),

                    /*
                    BulkAction::make('add_tags')
                        ->label('Ajouter un mot-clé')
                        ->icon('heroicon-o-tag')
                        ->action(function (Collection $records, array $data): void {

                            $bookIds = $records->pluck('id')->toArray();

                            dd($data, $bookIds);

                            $bookIds = $records->pluck('id')->toArray();
                            $selectedTags = $data['tags'];

                            

                            foreach ($selectedTags as $tag) {
                                if(!$Tag = Tag::where('title', $tag)->first()) 
                                {
                                    $Tag = Tag::create([
                                        'title' => $tag,
                                        'slug' => Str::slug($tag),
                                    ]);
                                }
            
                                foreach ($records as $record) {
                                    $record->tags()->attach($Tag->id);
                                }
                            }   
                        })
                        ->form([
                            Forms\Components\Select::make('tags')
                                ->label('Mots-clés')
                                ->multiple()
                                ->relationship('tags', 'title')
                                ->preload()
                                ->createOptionForm([
                                    Forms\Components\TextInput::make('title')
                                        ->label('Nom')
                                ])
                        ])
                        ->action(function (Collection $records, array $data): void {

                            dd($records->pluck('id')->toArray(), $data);
                            $bookIds = $records->pluck('id')->toArray();
                            $selectedTags = $data['tags'];

                            foreach ($selectedTags as $tag) {
                                if(!$Tag = Tag::where('title', $tag)->first()) 
                                {
                                    $Tag = Tag::create([
                                        'title' => $tag,
                                        'slug' => Str::slug($tag),
                                    ]);
                                }
            
                                foreach ($records as $record) {
                                    $record->tags()->attach($Tag->id);
                                }
                            }   
                        }),
                    */

                    BulkAction::make('lang')
                        ->label('Saisir la langue')
                        ->icon('heroicon-o-language')
                        ->form([
                            Forms\Components\Select::make('lang')
                                ->label('Langue')
                                ->required()
                                ->options([
                                    'fr' => 'Français',
                                    'en' => 'Anglais',
                                ])
                        ])
                        ->action(fn (Collection $records, array $data) => $records->each(function (Book $record) use ($data) {
                            $record->lang = $data['lang'];
                            $record->save();
                        })),

                    BulkAction::make('generateQrCodes')
                        ->label('Générer les QR codes')
                        ->icon('heroicon-o-qr-code')
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
                ]),

            ])
            ->defaultPaginationPageOption(50)
            ->paginationPageOptions([50, 100, 200])
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

                Tables\Filters\SelectFilter::make('lang')
                    ->label('Langue')
                    ->options([
                        'fr' => 'Français',
                        'en' => 'Anglais',
                    ])
                    ->default(null),    
                    
                Filter::make('lang_null')
                    ->form([
                        Forms\Components\Checkbox::make('lang_null')
                            ->label('Langue non renseignée'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['lang_null'],
                            fn (Builder $query): Builder => $query->whereNull('lang')
                        );
                    }),                    

                Tables\Filters\SelectFilter::make('missing')
                    ->label('Manquant')
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


                Filter::make('description_null')
                    ->form([
                        Forms\Components\Checkbox::make('description_null')
                            ->label('Description vide'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['description_null'],
                            fn (Builder $query): Builder => $query->whereNull('description')
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
