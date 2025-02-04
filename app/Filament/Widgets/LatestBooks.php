<?php

namespace App\Filament\Widgets;

use App\Models\Book;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LatestBooks extends BaseWidget
{
    protected static ?int $sort = 4; // Position après le FilamentInfoWidget
    protected static ?int $defaultTableRecordsPerPage = 5;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Book::query()
                    ->latest('created_at')
                    ->limit(5)
            )
            ->heading('Derniers livres ajoutés 📚')
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Titre')
                    ->searchable()
                    ->sortable(),
                    Tables\Columns\ImageColumn::make('cover_url')
                    ->label('Couverture')
                    ->sortable()
                    ->height(100),


                Tables\Columns\TextColumn::make('author')
                    ->label('Auteur')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Ajouté le')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ]);
    }
} 