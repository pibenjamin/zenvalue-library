<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TrainingResource\Pages;
use App\Filament\Resources\TrainingResource\RelationManagers;
use App\Models\Training;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use App\Models\Book;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\Grid;
use Filament\Tables\Enums\ActionsPosition;
use App\Filament\Resources\TrainingResource\Widgets\BookTrainingsWidget;
use App\Filament\Resources\TrainingResource\Widgets\LinksTrainingsWidget;
use App\Filament\Resources\TrainingResource\Widgets\DocsTrainingsWidget;
use Filament\Infolists\Components\Split;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Illuminate\Support\Str;
class TrainingResource extends Resource
{
    protected static ?string $model                 = Training::class;
    protected static ?string $modelLabel            = 'Formation';
    protected static ?string $pluralModelLabel      = 'Formations';
    protected static ?string $navigationGroup       = 'Matching avec les formations';
    protected static ?int $navigationSort           = 1;
    protected static ?string $navigationIcon        = 'heroicon-o-academic-cap';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Section 1 : Informations de la formation
                Forms\Components\Section::make('Informations de la formation')
                    ->schema([
                        Forms\Components\Grid::make()
                            ->schema([
                                // Colonne gauche (titre, url, description)
                                Forms\Components\Grid::make()
                                    ->schema([
                                        Forms\Components\TextInput::make('title')
                                            ->label('Titre')
                                            ->required()
                                            ->maxLength(255)
                                            ->columnSpan('full'),

                                        Forms\Components\Select::make('trainers')
                                            ->label('Formateurs')
                                            ->multiple()
                                            ->relationship('trainers', 'name')
                                            ->preload()
                                            ->columnSpan('full'),

                                        Forms\Components\TextInput::make('url')
                                            ->label('URL Catalogue')
                                            ->required()
                                            ->maxLength(255)
                                            ->columnSpan('full'),
                                        Forms\Components\Textarea::make('description')
                                            ->label('Description')
                                            ->required()
                                            ->columnSpan('full'),
                                    ])
                                    ->columnSpan(['lg' => 2]),

                                // Colonne droite (image)
                                Forms\Components\FileUpload::make('image')
                                    ->label('Image')
                                    ->directory('trainings')
                                    ->maxSize(5120) // 5MB
                                    ->required()
                                    ->columnSpan(['lg' => 1]),
                            ])
                            ->columns(['lg' => 3]),
                    ]),

                // Section 2 : Ressources associées
                Forms\Components\Section::make('Ressources associées')
                    ->schema([
                        Forms\Components\Select::make('books')
                            ->label('Livres')
                            ->multiple()
                            ->relationship('books', 'title')
                            ->preload()
                            ->getSearchResultsUsing(fn (string $search): array => 
                                Book::where('status', Book::STATUS_ON_SHELF)
                                    ->where('missing', false)
                                    ->where('title', 'like', "%{$search}%")
                                    ->limit(50)
                                    ->pluck('title', 'id')
                                    ->toArray())
                                    ->live(),
                        Forms\Components\Select::make('docs')
                            ->label('Documents')
                            ->multiple()
                            ->relationship('docs', 'name')
                            ->preload()
                            ->createOptionForm([
                                Forms\Components\TextInput::make('name')    
                                    ->label('Nom')
                                    ->placeholder('Nom du document')
                                    ->unique(ignoreRecord: true)
                                    
                                    ->required(),
                                Forms\Components\FileUpload::make('path')
                                    ->label('Fichier')
                                    ->directory('trainings/docs')
                                    ->placeholder('Chemin du fichier')
                                    ->unique(ignoreRecord: true)
                                    ->required(),
                            ]),
                        Forms\Components\Select::make('links')
                            ->label('Liens')
                            ->multiple()
                            ->relationship('links', 'name')
                            ->preload()
                            ->createOptionForm([
                                Forms\Components\TextInput::make('name')
                                    ->label('Nom')
                                    ->placeholder('Nom du lien')
                                    ->unique(ignoreRecord: true)
                                    ->required(),
                                Forms\Components\TextInput::make('url')
                                    ->label('Adresse du lien')
                                    ->placeholder('https://www.exemple.com/article')
                                    ->unique(ignoreRecord: true)
                                    ->required(),
                            ]),
                    ]),
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
                    ->width(300)
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('url')
                    ->state(function (Training $record): string {
                        return 'ouvrir';
                    })
                    ->label('Lien')
                    ->icon('heroicon-o-link')
                    ->url(fn (Training $record) => $record->url)
                    ->openUrlInNewTab()
                    ->sortable()
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('trainers.name')
                    ->label('Formateurs')
                    ->sortable()
                    ->badge()
                    ->toggleable(isToggledHiddenByDefault: false)
                    ->searchable(),
                TextColumn::make('books.title')
                    ->label('Livres')
                    ->sortable()
                    ->listWithLineBreaks()
                    ->limitList(3)
                    ->expandableLimitedList()
                    ->searchable()
                    ->color('stone')
                    ->state(function (Training $record): string {
                        return Str::words($record->books->pluck('title')->implode(', '), 5);
                    })
                    ->badge()
                    ->toggleable(),
                TextColumn::make('trainers.name')
                    ->label('Formateurs')
                    ->sortable()
                    ->badge()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
                ImageColumn::make('image')
                    ->label('Image')
                    ->sortable()
                    ->searchable()
                    ->toggleable(),
            ])
            ->filters([
                // ajoute moi un filtre pour n'afficher que les formations avec des formateurs
                SelectFilter::make('trainers')
                    ->label('Formateurs')
                    ->multiple()
                    ->relationship('trainers', 'name')
                    ->preload()
                    ->searchable(),
                    
                Filter::make('no_trainers')
                    ->form([
                        Forms\Components\Checkbox::make('no_trainers')
                            ->label('Avec formateur(s)'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['no_trainers'],
                            fn (Builder $query): Builder => $query->whereHas('trainers')
                        );
                    }),


            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),
            ])
            ->actionsPosition(ActionsPosition::BeforeColumns)

            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    //Tables\Actions\ForceDeleteBulkAction::make(),
                ]),
            ])
            ->defaultPaginationPageOption(50)
            ->paginationPageOptions([25, 50, 100]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Split::make([
                    Section::make([
                        TextEntry::make('title')
                            ->label('')
                            ->weight(FontWeight::Bold)
                            ->formatStateUsing(fn (string $state): string => "<h2 class='text-2xl'>{$state}</h2>")
                            ->html(),
                        TextEntry::make('trainers.name')
                            ->label('Formateurs')
                            ->badge(),
                        TextEntry::make('url')
                            ->label('')
                            ->url(fn (Training $record) => $record->url)
                            ->color('primary')
                            ->openUrlInNewTab(),
                    ])
                    ->columnSpan(2),
                    Section::make([
                        ImageEntry::make('image')
                            ->label('')
                            ->width(200)
                    ])
                    ->grow(false)
                    ->columnSpan(1),
                ])
                ->from('md')
                ->columnSpan('full'),
            ]);
    }


    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getWidgets(): array
    {
        return [
            BookTrainingsWidget::class,
            LinksTrainingsWidget::class,
            DocsTrainingsWidget::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTrainings::route('/'),
            'create' => Pages\CreateTraining::route('/create'),
            'edit' => Pages\EditTraining::route('/{record}/edit'),
            'view' => Pages\ViewTraining::route('/{record}'),
        ];
    }
}
