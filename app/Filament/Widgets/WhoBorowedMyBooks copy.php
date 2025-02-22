<?php

namespace App\Filament\Widgets;

use App\Models\Loan;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;



class WhoBorowedMyBooks extends BaseWidget
{
    protected static ?int $sort = 3; // Position après le FilamentInfoWidget
    protected static ?int $defaultTableRecordsPerPage = 5;

    public static function canView(): bool
    {
        return auth()->user()->hasRole('user');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Loan::query()
                    ->whereHas('book', function ($query) {
                        $query->where('owner_id', auth()->id());
                    })
                    ->latest('borrowed_at')
                    ->limit(3)
            )
            ->heading('Qui a emprunté mes livres ? 📚')
            ->columns([
                Tables\Columns\TextColumn::make('book.title')
                    ->label('Livre')
                    ->wrap()
                    ->sortable(),
                Tables\Columns\ImageColumn::make('cover_url')
                    ->label('Couverture')
                    ->sortable()
                    ->height(100),
                Tables\Columns\TextColumn::make('borrower.name')
                    ->label('Emprunté par')
                    ->sortable(),
                Tables\Columns\TextColumn::make('borrowed_at')
                    ->label('Emprunté le')
                    ->dateTime('d/m/Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->label('Statut')
                    ->badge(),
            ])
            ->paginated([3, 'all']);
    }
} 