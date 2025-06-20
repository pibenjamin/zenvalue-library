<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AquisitionRequestResource\Pages;
use App\Filament\Resources\AquisitionRequestResource\RelationManagers;
use App\Models\AquisitionRequest;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Actions\Action;
use Illuminate\Support\HtmlString;
use Filament\Forms\Components\Placeholder;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use App\Services\ImportBookData;
use Filament\Forms\Get;

class AquisitionRequestResource extends Resource
{
    protected static ?string $model = AquisitionRequest::class;

    protected static ?string $navigationLabel       = 'Demande d\'acquisition';
    protected static ?string $pluralNavigationLabel = 'Demandes d\'acquisition';
    protected static ?string $navigationGroup       = 'Gestion du catalogue';

    protected static ?string $modelLabel            = 'une demande d\'acquisition';
    protected static ?string $pluralModelLabel      = 'Demandes d\'acquisition';
    protected static ?int $navigationSort           = 4;

    protected static ?string $navigationIcon        = 'heroicon-o-plus';



    public static function getNavigationBadge(): ?string
    {
        return AquisitionRequest::where('status', 'pending')->count();
    }
    
    public static function getNavigationBadgeTooltip(): ?string
    {
        return 'Nombre de demandes d\'acquisition en attente';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Grid::make()
                    ->columns(2)
                    ->schema([
                        Forms\Components\Section::make('Motivation de la demande')
                            ->columnSpan(1)
                            ->schema([

                                Forms\Components\Radio::make('status')
                                    ->label('Etat de la demande')
                                    ->options(AquisitionRequest::getStatusLabelsForAdmin())
                                    ->inline()
                                    ->default('pending')
                                    ->visible(fn () => 
                                        auth()->user()->hasRole('super_admin')
                                    )
                                    ->reactive(),
                                    
                                Forms\Components\Textarea::make('reject_reason')
                                    ->label('Motif du rejet')
                                    ->placeholder('Veuillez nous indiquer le motif de rejet de la demande.')
                                    ->rows(9)
                                        ->visible(function (?AquisitionRequest $record = null, Get $get): bool {
                                            if (!$record) return false;

                                            if($get('status') === 'rejected') {
                                                return true;
                                            }
                                            return false;
                                    }),

                                Forms\Components\Textarea::make('description')
                                    ->placeholder('Selon vous en quoi ce livre serait utile à vous-même mais également à la communauté des citizens. Exemple : nous donnons des formations sur le sujet Y mais nous n\'avons aucune référence à ce sujet ; ce sujet n\'est pas répérensé dans notre bibliothèque.')
                                    ->required()
                                    ->rows(9),
                            ]),

                        Forms\Components\Section::make('Informations essentielles')
                            ->columnSpan(1)
                            ->schema([
                                Placeholder::make('link_example')
                                ->label('Merci de nous fournir un lien vers le livre que vous nous proposez d\'acquérir.')
                                ->content(new HtmlString(' Nous privilégions les librairies indépendantes comme <a href="https://www.lalibrairie.com" target="_blank" class="underline text-primary-600 hover:text-primary-500">www.lalibrairie.com</a>.'))
                                ->columnSpanFull(),

                                Forms\Components\TextInput::make('isbn')
                                ->label('ISBN')
                                ->required()
                                ->helperText(function ($record) {
                                    return new HtmlString('Récupérer les informations depuis Google Books');
                                })
                                ->maxLength(255)
                                ->prefixIcon('heroicon-o-globe-alt')
                                ->suffixAction(
                                    Forms\Components\Actions\Action::make('importFromGoogleBooks')
                                        ->label('Importer les informations')
                                        ->icon('heroicon-o-arrow-down-tray')
                                        ->disabled(fn (?AquisitionRequest $record): bool => !$record || $record->parsed === true)
                                        ->action(function (?AquisitionRequest $record) {
                                            if (!$record) return;
                                            app(\App\Services\GoogleBooksService::class)->importBookData($record);
                                        }),
                                ),

                                Forms\Components\TextInput::make('price')
                                    ->label('Prix estimé')
                                    ->placeholder('A titre indicatif avez une idée du prix du livre ?')
                                    ->prefix('€')
                                    ->numeric()
                                    ->maxLength(255),
                            ]),


                        Forms\Components\Section::make('Informations complémentaires')
                            ->description('Merci de nous fournir les informations suivantes pour que nous puissions vous aider à trouver le livre.')
                            ->columnSpan(2)
                            ->schema([
                                Forms\Components\TextInput::make('title')
                                    ->label('Titre')
                                    ->maxLength(255)
                                    ->columnSpan(1),

                                Forms\Components\Select::make('user')
                                    ->label('Auteur')
                                    ->relationship('author', 'name')
                                    ->preload()
                                    ->searchable()
                                    ->createOptionForm([
                                        Forms\Components\TextInput::make('name')
                                            ->label('Nom')
                                            ->unique()
                                            ->required(),
                                    ]),

                                Forms\Components\TextInput::make('isbn')
                                    ->helperText('L\'ISBN est l\'identifiant unique d\'un livre, il se compose de 13 chiffres parfois incluant un tiret en 3eme position :  9782226254764  ou  978-2226254764 ')
                                    ->label('ISBN')
                                    ->maxLength(14),

                                    Forms\Components\Hidden::make('user_id')
                                        ->default(auth()->id()),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                ->label('Titre')
                ->sortable()
                ->wrap()
                ->searchable(),

                Tables\Columns\TextColumn::make('user.name')
                ->label('Demandeur')
                ->color('gray')
                ->state(fn (AquisitionRequest $record): string => $record->user->id == auth()->id() ? 'Moi même' : $record->user->name)
                ->badge(),
                
                Tables\Columns\TextColumn::make('status')
                ->badge()
                ->color(fn (AquisitionRequest $record): string => $record->getStatusColor())
                ->state(fn (AquisitionRequest $record): string => $record->getStatusLabel()),

                Tables\Columns\TextColumn::make('created_at')
                ->label('Date de demande')
                ->dateTime('d/m/Y')
                ->badge(),
                
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                ->icon('heroicon-o-eye'),
                Tables\Actions\EditAction::make()
                ->icon('heroicon-o-pencil')
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(fn (AquisitionRequest $record) => 
                            auth()->user()?->hasRole('super_admin')
                        )
                    ]),
                ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    /*
    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\TextEntry::make('title'),
                Infolists\Components\TextEntry::make('user.name'),
                Infolists\Components\TextEntry::make('status'),
                Infolists\Components\TextEntry::make('created_at')
                    ->dateTime('d/m/Y'),
            ]);
    }
    */

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAquisitionRequests::route('/'),
            //'view' => Pages\ViewAquisitionRequest::route('/{record}'),
            'create' => Pages\CreateAquisitionRequest::route('/create'),
            'edit' => Pages\EditAquisitionRequest::route('/{record}/edit'),
        ];
    }
}
