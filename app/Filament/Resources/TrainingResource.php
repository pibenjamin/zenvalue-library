<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TrainingResource\Pages;
use App\Filament\Resources\TrainingResource\RelationManagers;
use App\Models\Training;
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
                            ->required()
                            ->live(),
                        Forms\Components\Select::make('docs')
                            ->label('Documents')
                            ->multiple()
                            ->relationship('docs', 'name')
                            ->preload()
                            ->required()
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
                            ->required()
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
                    ->searchable(),
                TextColumn::make('url')
                    ->state(function (Training $record): string {
                        return 'ouvrir';
                    })
                    ->label('Lien')
                    ->icon('heroicon-o-link')
                    ->url(fn (Training $record) => $record->url)
                    ->openUrlInNewTab()
                    ->sortable()
                    ->searchable(),
                TextColumn::make('books.title')
                    ->label('Livres')
                    ->sortable()
                    ->listWithLineBreaks()
                    ->limitList(3)
                    ->expandableLimitedList()
                    ->searchable(),
                ImageColumn::make('image')
                    ->label('Image')
                    ->sortable()
                    ->searchable(),
            ])
            ->filters([
                //
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
                Section::make()
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                // Colonne gauche : Informations de la formation
                                Grid::make()
                                    ->schema([
                                        TextEntry::make('title')
                                            ->label('Titre')
                                            ->columnSpan('full'),

                                        ImageEntry::make('image')
                                            ->label('Image')
                                            ->columnSpan('full'),

                                    ])
                                    ->columnSpan(1),

                                // Colonne droite : Ressources associées
                                Grid::make()
                                    ->schema([
                                        TextEntry::make('books.title')
                                            ->label('Livres associés')
                                            ->listWithLineBreaks()
                                            ->bulleted()
                                            ->columnSpan('full'),
                                        TextEntry::make('links.name')
                                            ->label('Liens')
                                            ->listWithLineBreaks()
                                            ->bulleted()
                                            ->columnSpan('full'),
                                        TextEntry::make('docs.name')
                                            ->label('Documents')
                                            ->listWithLineBreaks()
                                            ->bulleted()
                                            ->columnSpan('full'),
                                    ])
                                    ->columnSpan(1),
                                Grid::make()
                                    ->schema([
                                        TextEntry::make('url')
                                            ->label('URL')
                                            ->url(fn (Training $record) => $record->url)
                                            ->openUrlInNewTab()
                                            ->color('primary')
                                            ->columnSpan('full'),                                        
                                        TextEntry::make('description')
                                            ->label('Description')
                                            ->markdown()
                                            ->columnSpan('full'),
                                    ])
                                    ->columnSpan('full'),

                            ]),
                    ])
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
