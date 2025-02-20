<?php

namespace App\Filament\Widgets;

use App\Models\Loan;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class MyBookBorrowers extends BaseWidget
{
    protected int | string | array $columnSpan = 'full';

    protected static ?int $sort = 2;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Loan::query()
                    ->join('books', 'loans.book_id', '=', 'books.id')
                    ->join('users as borrowers', 'loans.borrower_id', '=', 'borrowers.id')
                    ->where('books.owner_id', auth()->id())
                    ->whereIn('status', ['returned'])
                    ->latest('borrowed_at')
                    ->selectRaw('borrowers.id, borrowers.name, COUNT(*) as total_books')
                    ->groupBy('borrowers.id', 'borrowers.name')
            )
            ->heading('Qui a emprunté mes livres ? 👥')
            ->description('Cette liste affiche les personnes qui ont emprunté vos livres, si le prêt est terminé.')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Emprunteur')
                    ->wrap()
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('total_books')
                    ->label('Nombre de livres empruntés')
                    ->sortable(),
            ])
            ->recordUrl(null);
    }
} 