<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ParcoursResource\Pages;
use App\Filament\Resources\ParcoursResource\RelationManagers;
use App\Filament\Resources\ParcoursResource\Widgets\BookParcoursWidget;
use App\Models\Parcours;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;

class ParcoursResource extends Resource
{
    protected static ?string $model = Parcours::class;

    protected static ?string $modelLabel            = 'Parcours';
    protected static ?string $pluralModelLabel      = 'Parcours';
    protected static ?string $navigationGroup       = 'Gestion des parcours';
    protected static ?int $navigationSort           = 1;
    protected static ?string $navigationIcon        = 'heroicon-o-map';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Grid::make()
                    ->schema([
                        Forms\Components\Section::make()
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->label('Nom')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\RichEditor::make('description')
                                    ->label('Description')
                                    ->required()
                                    
                            ])
                            ->columnSpan(2),
                        Forms\Components\Grid::make()
                            ->schema([
                                Forms\Components\Select::make('status')
                                    ->label('Statut')
                                    ->options(Parcours::getStatusLabels())
                                    ->default(Parcours::STATUS_ONLINE)
                            ])
                            ->columnSpan(1),
                    ])
                    ->columns(3),
                    Forms\Components\Grid::make()
                    ->schema([
                        Forms\Components\Select::make('books')
                            ->label('Livres')
                            ->multiple()
                            ->relationship('books', 'title')
                            ->columnSpanFull()
                            ->visible(function () {
                                return auth()->user()->hasRole('super_admin');
                            })
                    ])
                    ->columnSpan(3),
                    Forms\Components\Grid::make()
                    ->schema([
                        Forms\Components\Select::make('users')
                            ->label('Utilisateurs')
                            ->multiple()
                            ->relationship('users', 'name')
                            ->columnSpanFull()
                            ->visible(function (Parcours $record) {
                                return auth()->user()->hasRole('super_admin');
                            })
                    ])
                    ->columnSpan(3),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nom')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('description')
                    ->label('Description')
                    ->sortable()
                    ->html()
                    ->tooltip(function (Parcours $record) {
                        return view('filament.components.tooltip-description', [
                            'description' => $record->description
                        ])->render();
                    })
                    ->words(10)
                    ->searchable(),
                Tables\Columns\TextColumn::make('user_state')
                    ->label('Utilisateurs')
                    ->badge()
                    ->sortable()
                    ->searchable()
                    ->state(function (Parcours $record) {
                        if(auth()->user()->hasRole('user')) {
                            if($record->users->contains(auth()->user())) {
                                return 'Parcours actuel';
                            }
                            if(auth()->user()->parcours()->exists()) {
                                return 'Indisponible pour l\'instant';
                            }
                            return 'Non inscrit';
                        }
                        return $record->users->count();
                    })
                    ->color(function (Parcours $record) {
                        if(auth()->user()->hasRole('user')) {
                            if($record->users->contains(auth()->user())) {
                                return 'success';
                            }
                            if(auth()->user()->parcours()->exists()) {
                                return 'danger';
                            }
                            return 'gray';
                        }
                        return 'info';
                    }),                    
                Tables\Columns\TextColumn::make('status')
                    ->label('Statut')
                    ->badge()
                    ->sortable()
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('subscribe')
                    ->label('S\'inscrire')
                    ->modalHeading('S\'inscrire au parcours')
                    ->modalDescription('En vous inscrivant à ce parcours, vous recevrez les recommandations de lecture.')
                    ->modalSubmitActionLabel('S\'inscrire')
                    ->color('success')
                    ->action(function (Parcours $record) {
                        $record->users()->attach(auth()->user());
                    })
                    ->visible(function (Parcours $record) {
                        return auth()->user()->hasRole('user') 
                            && !$record->users->contains(auth()->user())
                            && !auth()->user()->parcours()->exists();
                    }),
                Tables\Actions\Action::make('unsubscribe')
                    ->label('Se désinscrire')
                    ->modalHeading('Se désinscrire du parcours')
                    ->modalDescription('En vous désinscrivant de ce parcours, vous ne recevrez plus les recommandations de lecture.')
                    ->modalSubmitActionLabel('Se désinscrire')
                    ->color('danger')
                    ->action(function (Parcours $record) {
                        $record->users()->detach(auth()->user());
                    })
                    ->visible(function (Parcours $record) {
                        return auth()->user()->hasRole('user') && $record->users->contains(auth()->user());
                    }),                    
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

    public static function getWidgets(): array
    {
        return [
            BookParcoursWidget::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListParcours::route('/'),
            'create' => Pages\CreateParcours::route('/create'),
            'edit' => Pages\EditParcours::route('/{record}/edit'),
            'view' => Pages\ViewParcours::route('/{record}'),
        ];
    }
}
