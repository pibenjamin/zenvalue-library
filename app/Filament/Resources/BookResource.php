<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BookResource\Pages;
use App\Filament\Resources\BookResource\RelationManagers;
use App\Models\Book;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class BookResource extends Resource
{
    protected static ?string $model = Book::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('slug')
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\TextInput::make('author')
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\TextInput::make('cover_url')
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\TextInput::make('google_api_page')
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\TextInput::make('isbn')
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\Toggle::make('is_borrowed')
                    ->required(),
                Forms\Components\Toggle::make('open_library_parsed')
                    ->required(),
                Forms\Components\TextInput::make('original_filename')
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\Select::make('owner_id')
                    ->relationship('owner', 'name')
                    ->required(),
                Forms\Components\TextInput::make('pages')
                    ->numeric()
                    ->default(null),
                Forms\Components\DatePicker::make('published_at'),
                Forms\Components\TextInput::make('publisher')
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\TextInput::make('quantity')
                    ->required()
                    ->numeric()
                    ->default(1),
                Forms\Components\Select::make('support_id')
                    ->relationship('support', 'name')
                    ->required(),
                Forms\Components\Select::make('theme_id')
                    ->relationship('theme', 'name')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable(),
                Tables\Columns\TextColumn::make('slug')
                    ->searchable(),
                Tables\Columns\TextColumn::make('author')
                    ->searchable(),
                Tables\Columns\TextColumn::make('cover_url')
                    ->searchable(),
                Tables\Columns\TextColumn::make('google_api_page')
                    ->searchable(),
                Tables\Columns\TextColumn::make('isbn')
                    ->searchable(),
                Tables\Columns\IconColumn::make('is_borrowed')
                    ->boolean(),
                Tables\Columns\IconColumn::make('open_library_parsed')
                    ->boolean(),
                Tables\Columns\TextColumn::make('original_filename')
                    ->searchable(),
                Tables\Columns\TextColumn::make('owner.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('pages')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('published_at')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('publisher')
                    ->searchable(),
                Tables\Columns\TextColumn::make('quantity')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('support.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('theme.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
            'index' => Pages\ListBooks::route('/'),
            'create' => Pages\CreateBook::route('/create'),
            'edit' => Pages\EditBook::route('/{record}/edit'),
        ];
    }
}
