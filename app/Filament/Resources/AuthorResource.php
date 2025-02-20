<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AuthorResource\Pages;
use App\Filament\Resources\AuthorResource\RelationManagers;
use App\Models\Author;
use App\Models\Book;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Models\User;
use Filament\Forms\Components\FileUpload;
class AuthorResource extends Resource
{
    protected static ?string $model             = Author::class;
    protected static ?string $modelLabel        = 'Auteur';
    protected static ?string $pluralModelLabel  = 'Auteurs';
    
    protected static ?string $navigationIcon    = 'heroicon-o-user-group';
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),

                Forms\Components\FileUpload::make('photo_url')
                    ->label('Photo')
                    ->directory('authors')
                    ->maxSize(5120) // 5MB
                    ->columnSpanFull(),


            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->visible(fn (User $user) => auth()->user()->hasAnyRole(['super_admin', 'admin'])),
                Tables\Columns\TextColumn::make('name')
                    ->label('Nom')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\ImageColumn::make('photo_url')
                    ->label('Photo')
                    ->circular()
                    ->url(fn (Author $record): string => $record->photo_url ? $record->photo_url : url('/authors/photo/author-placeholder.jpeg'))
                    ->height(50),

                Tables\Columns\TextColumn::make('books_count')
                    ->label('# livres')
                    ->counts('books')
                    ->sortable(),

                Tables\Columns\TextColumn::make('books.title')
                    ->label('Livres')
                    ->badge()
                    ->openUrlInNewTab()
                    ->wrap()
                    ->tooltip(fn (Author $record) => $record->books->pluck('title')->implode(' - '))
                    ->url(fn (Author $record) => url('/admin/books?tableSearch=&tableFilters[authors][name][value]=' . $record->id))
                    ->openUrlInNewTab()
                    ->listWithLineBreaks()
                    ->limit(20)
                    ->sortable(),
            ])
            ->defaultPaginationPageOption(200)
            ->defaultSort('name', 'asc')
            ->paginationPageOptions([200, 500, 1000])
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
            'index' => Pages\ListAuthors::route('/'),
            'create' => Pages\CreateAuthor::route('/create'),
            'edit' => Pages\EditAuthor::route('/{record}/edit'),
        ];
    }
}
