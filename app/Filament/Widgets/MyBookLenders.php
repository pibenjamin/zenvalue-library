<?php

namespace App\Filament\Widgets;

use App\Models\Loan;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;

class MyBookLenders extends BaseWidget
{
    protected static ?int $sort = 3; 
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
                ->where('borrower_id', auth()->id())
                ->whereIn('status', ['returned'])
                ->latest('borrowed_at')
                ->join('books', 'loans.book_id', '=', 'books.id')
                ->join('users as lenders', 'books.owner_id', '=', 'lenders.id')
                ->selectRaw('lenders.id, lenders.name, COUNT(*) as total_books')
                ->groupBy('lenders.id', 'lenders.name')
        )
        ->heading('A qui ai-je emprunté ? 📚')
        ->columns([
            Tables\Columns\TextColumn::make('name')
                ->label('Prêteur')
                ->wrap()
                ->sortable(),
                
            Tables\Columns\TextColumn::make('total_books')
                ->label('Nombre de livres empruntés')
                ->sortable(),
        ])
        ->recordUrl(null);
    }


} 