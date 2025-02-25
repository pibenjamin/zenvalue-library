<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TagResource\Pages;
use App\Filament\Resources\TagResource\RelationManagers;
use App\Models\Tag;
use App\Models\Book;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TagResource extends Resource
{
    protected static ?string $model = Tag::class;
    protected static ?string $modelLabel = 'Mot-clé';
    protected static ?string $pluralModelLabel = 'Mots-clés';    
    protected static ?string $navigationIcon = 'heroicon-o-tag';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->label('Nom')
                    ->placeholder('Nom du mot-clé')
                    ->helperText('Le nom du mot-clé est obligatoire')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([

                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->visible(fn () => auth()->user()->hasRole('super_admin')),


                Tables\Columns\TextColumn::make('title')
                    ->label('Nom')
                    ->wrap()
                    ->searchable(),

                Tables\Columns\TextColumn::make('slug')
                    ->label('Slug')
                    ->wrap()
                    ->searchable()
                    ->visible(fn () => auth()->user()->hasRole('super_admin')),

                Tables\Columns\TextColumn::make('books_count')
                    ->label('Nombre de livres')
                    ->counts('books')
                    ->sortable(),

                Tables\Columns\TextColumn::make('books.title')
                    ->label('Livres')
                    ->tooltip(fn (Tag $record) => $record->books->pluck('title')->implode(' - '))
                    ->url(fn (Tag $record) => url('/admin/books?tableFilters[tags][title][values][0]=' . $record->id))
                    ->openUrlInNewTab()
                    ->listWithLineBreaks()
                    ->badge()
                    ->color('gray')
                    ->wrap()
                    ->searchable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('books.title')
                ->label('Livres')
                ->relationship('books', 'title')
                ->options(Book::all()->pluck('title', 'id')),


                ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->defaultPaginationPageOption(200)
            ->paginationPageOptions([200, 500, 1000])

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
            'index' => Pages\ListTags::route('/'),
            'create' => Pages\CreateTag::route('/create'),
            'edit' => Pages\EditTag::route('/{record}/edit'),
        ];
    }
}
