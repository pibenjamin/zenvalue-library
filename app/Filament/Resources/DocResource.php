<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DocResource\Pages;
use App\Filament\Resources\DocResource\RelationManagers;
use App\Models\Doc;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
class DocResource extends Resource
{
    protected static ?string $model                 = Doc::class;
    protected static ?string $navigationIcon        = 'heroicon-o-document';
    protected static ?string $navigationGroup       = 'Matching avec les formations';
    protected static ?int $navigationSort           = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Nom')
                    ->required()
                    ->maxLength(255),
                Forms\Components\FileUpload::make('path')
                    ->label('Fichier')
                    ->directory('trainings/docs')
                    ->required(),
                Forms\Components\Select::make('authors')
                    ->label('Auteurs')
                    ->multiple()
                    ->relationship('authors', 'name')
                    ->preload(),
                Forms\Components\Select::make('trainings')
                    ->label('Formation')
                    ->multiple()
                    ->relationship('trainings', 'title')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nom'),
                Tables\Columns\TextColumn::make('path')
                    ->label('Fichier'),
                Tables\Columns\TextColumn::make('authors.name')
                    ->label('Auteur'),
                Tables\Columns\TextColumn::make('trainings.title')
                    ->label('Formation'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Date de création')
                    ->dateTime('d-m-Y'),
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
            'index' => Pages\ListDocs::route('/'),
            'create' => Pages\CreateDoc::route('/create'),
            'edit' => Pages\EditDoc::route('/{record}/edit'),
        ];
    }
}
