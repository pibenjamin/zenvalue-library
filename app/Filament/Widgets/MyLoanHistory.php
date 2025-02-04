<?php

namespace App\Filament\Widgets;

use App\Models\Loan;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;

class MyLoanHistory extends BaseWidget
{
    protected static ?int $sort = 3; // Position après le FilamentInfoWidget
    protected static ?int $defaultTableRecordsPerPage = 5;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Loan::query()
                    ->where('borrower_id', auth()->id())
                    ->latest('borrowed_at')
            )
            ->heading('Mon historique de prêts 📚')
            ->columns([
                Tables\Columns\TextColumn::make('book.title')
                    ->label('Livre')
                    ->wrap()
                    ->sortable(),

                    Tables\Columns\ImageColumn::make('book.cover_url')
                    ->label('Couverture')
                    ->sortable()
                    ->height(100),

                Tables\Columns\TextColumn::make('returned_at')
                    ->label('Retourné le')
                    ->dateTime('d/m/Y')
                    ->default('-'),
                Tables\Columns\TextColumn::make('status')
                    ->label('Statut')
                    ->getStateUsing(function (Loan $record) {
                        return $record->getStatusLabel();
                    })
                    ->color(function (Loan $record) {
                        return $record->getStatusColor();
                    })
                    ->badge(),
            ]);
    }


} 