<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TutorielResource\Pages;
use App\Filament\Resources\TutorielResource\RelationManagers;
use App\Models\Tutoriel;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\RichEditor;
use Spatie\EloquentSortable\SortableTrait;

class TutorielResource extends Resource
{
    use SortableTrait;

    protected static ?string $model                 = Tutoriel::class;
    protected static ?string $modelLabel            = 'Tutoriel vidéo';
    protected static ?string $pluralModelLabel      = 'Tutoriels vidéo';    
    protected static ?string $navigationGroup       = 'Gestion du contenu';
    protected static ?int $navigationSort           = 3;
    protected static ?string $navigationIcon        = 'heroicon-o-video-camera';    

    public static function form(Form $form): Form
    {
        return $form
        ->schema([
            Forms\Components\Grid::make()
                ->schema([
                    // Section principale (2/3 de la largeur)
                    Forms\Components\Section::make('Informations de la formation')
                        ->schema([
                            Forms\Components\TextInput::make('titre')
                                ->required()
                                ->maxLength(255),
                            Forms\Components\RichEditor::make('description')
                                ->required(),
                            Forms\Components\TextInput::make('video_url')
                                ->required()
                                ->maxLength(255)
                                ->dehydrateStateUsing(function ($state) {
                                    $state = str_replace('https://clip.place/w/', 'https://clip.place/videos/embed/', $state);
                                    return $state;
                                })
                                ->live()
                        ])
                        ->columnSpan(2),

                    // Statut (1/3 de la largeur)
                    Forms\Components\Card::make()
                        ->schema([
                            Forms\Components\Toggle::make('is_active')
                                ->default(true)
                                ->label('Statut')
                        ])
                        ->columnSpan(1),
                ])
                ->columns(3),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
        ->query(
            Tutoriel::query()
                ->orderBy('order')
        )
            ->columns([
                Tables\Columns\TextColumn::make('titre')
                    ->label('Titre')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('video_url')
                    ->label('URL de la vidéo')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\ToggleColumn::make('is_active')
                    ->label('Statut')
                    ->sortable()
                    ->searchable(),
                    
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->reorderable('order');
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
            'index' => Pages\ListTutoriels::route('/'),
            'create' => Pages\CreateTutoriel::route('/create'),
            'edit' => Pages\EditTutoriel::route('/{record}/edit'),
        ];
    }
}
