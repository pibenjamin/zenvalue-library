<?php

namespace App\Filament\Widgets;

use App\Models\Loan;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;

class MyBookLenders extends BaseWidget
{
    use HasWidgetShield;

    protected static ?int $sort = 3; 
    protected static ?int $defaultTableRecordsPerPage = 5;

    protected function getHeading(): ?string
    {
        return 'A qui ai-je emprunté ?';
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
                ->limit(3)
        )
        ->heading('A qui ai-je emprunté ? 👤')
        ->description('Cette liste affiche les personnes à qui vous avez emprunté des livres, si le prêt est terminé.')
        ->columns([
            Tables\Columns\TextColumn::make('name')
                ->label('Prêteur')
                ->wrap()
                ->sortable(),
                
            Tables\Columns\TextColumn::make('total_books')
                ->label('Nombre de livres empruntés')
                ->sortable(),
        ])
        ->paginated([3, 'all']);
    }


} 