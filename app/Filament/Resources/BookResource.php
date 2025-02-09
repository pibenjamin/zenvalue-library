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
use App\Models\Notification;
use App\Services\LoanService;

use Illuminate\Contracts\View\View;


use Filament\Resources\Components\Tab;
use Filament\Tables\Actions\Action;
use Illuminate\Support\Facades\Http;

class BookResource extends Resource
{
    protected static ?string $model             = Book::class;
    protected static ?string $modelLabel        = 'Ouvrage';
    protected static ?string $pluralModelLabel  = 'Ouvrages';
    
    protected static ?string $navigationIcon    = 'heroicon-o-book-open';

/*
    public function getTabs(): array
    {
        return [
            'all' => Tab::make('Tous les ouvrages'),
            'borrowed' => Tab::make('Empruntés')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('is_borrowed', true)),
            'available' => Tab::make('Disponibles')

                ->modifyQueryUsing(fn (Builder $query) => $query->where('is_borrowed', false)),

        ];
    }

    public function getDefaultActiveTab(): string | int | null
    {
        return 'available';
    }
*/

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
                Forms\Components\Select::make('tags')
                    ->label('Tags')
                    ->multiple()
                    ->relationship('tags', 'title')
                    ->preload()

                    ->createOptionForm([
                        Forms\Components\TextInput::make('name')
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

                Forms\Components\DatePicker::make('published_at')
                    ->label('Date de publication')
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
                    ->relationship('theme', 'name')

            ]);
    }


    public static function table(Table $table): Table
    {
        return $table
        
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Titre')
                    ->sortable()
//                    ->width('22%')
//                    ->extraAttributes([
//                        'style' => 'max-width:600px'
//                    ])
                    ->wrap()
                    ->searchable(),
//                Tables\Columns\TextColumn::make('slug')
//                    ->searchable(),
                Tables\Columns\TextColumn::make('authors.name')
                ->label('Auteurs')
                ->wrap()
                ->searchable(),
//                Tables\Columns\TextColumn::make('tags.title')
//                ->label('Tags')
//                ->wrap()
//                ->searchable(),



                Tables\Columns\ImageColumn::make('cover_url')
                    ->label('Couverture')
                    ->sortable()
                    ->height(100),
//                    ->defaultImageUrl(url('/images/no-cover.jpg')),
//                Tables\Columns\TextColumn::make('google_api_page')
//                    ->label('Page Google')
//                    ->searchable(),
//                Tables\Columns\TextColumn::make('isbn')
//                    ->label('ISBN')
//                    ->searchable(),



                Tables\Columns\IconColumn::make('is_borrowed')
                    ->label('Emprunté')
                    ->boolean(),
//                Tables\Columns\IconColumn::make('open_library_parsed')
//                ->disabled(false)
//                ->label('Open Library')
//                    ->boolean(),

//                Tables\Columns\TextColumn::make('original_filename')
//                ->disabled(false)
//                ->searchable(),
//                Tables\Columns\TextColumn::make('owner.name')
//                    ->label('Propriétaire')
//                    ->wrap()
//                    ->numeric()
//                    ->sortable(),

//                Tables\Columns\TextColumn::make('pages')
//                    ->label('# Pages')
//                    ->numeric()
//                    ->sortable(),

                Tables\Columns\TextColumn::make('published_at')
                    ->date('Y')
                    ->label('Année')
                    ->sortable(),
//                Tables\Columns\TextColumn::make('publisher')
//                    ->label('Editeur')
//                    ->searchable(),
//                Tables\Columns\TextColumn::make('quantity')
//                    ->label('# Exemplaires')
//                    ->numeric()
//                    ->sortable(),

//                Tables\Columns\TextColumn::make('support.name')
//                    ->label('Support')
//                    ->numeric()
//                    ->sortable(),

                Tables\Columns\TextColumn::make('theme.name')
                    ->label('Thème')
                    ->numeric()
                    ->sortable(),
//                Tables\Columns\TextColumn::make('created_at')
//                    ->dateTime()
//                    ->label('Date de création')
//                    ->sortable()
//                    ->toggleable(isToggledHiddenByDefault: true),//

//                Tables\Columns\TextColumn::make('updated_at')
//                    ->dateTime()
//                    ->label('Date de modification')
//                    ->sortable()
//                    ->toggleable(isToggledHiddenByDefault: true),


            ])
            ->defaultPaginationPageOption(200)
            ->paginationPageOptions([200, 500, 1000])

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

                Tables\Filters\SelectFilter::make('tags.title')
                    ->label('Tags')
                    ->multiple()
                    ->relationship('tags', 'title')                    
            ])
            ->actions([
                
                Tables\Actions\EditAction::make(),
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
                    
                    ->visible(fn (Book $record) => ($record->isbn !== null)),

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
                    ->visible(fn (Book $book) => !$book->is_borrowed),

                Tables\Actions\Action::make('return')
                    ->label('Rendre')
                    ->color('success')
                    ->icon('heroicon-s-arrow-uturn-left')
                    ->requiresConfirmation()
                    ->modalHeading('Rendre ce livre')
                    ->modalDescription(fn (Book $book) => "Voulez-vous rendre {$book->title} ?")
                    ->action(function (Book $book) {
                        app(LoanService::class)->userSignaleReturn($book);
                    })
                    ->visible(fn (Book $book) => $book->is_borrowed)

            ])

            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);

    }

    public static function getRelations(): array
    {
        return [
            //
        ];
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
