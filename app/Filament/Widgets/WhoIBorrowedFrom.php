<?php

namespace App\Filament\Widgets;

use App\Models\Loan;
use App\Models\User;
use App\Models\Book;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;

class WhoIBorrowedFrom extends BaseWidget
{
    use HasWidgetShield;

    protected int | string | array $columnSpan = '1';

    protected static ?int $sort = 2;

    protected function getHeading(): ?string
    {
        return 'Qui a emprunté mes livres ?';
    }

    
    public static function canView(): bool
    {
        $loans = Loan::where('borrower_id', auth()->id())->get();

        if($loans->count() > 0) {
            return true;
        }

        return false;

    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Loan::query()
                    ->join('books', 'loans.book_id', '=', 'books.id')
                    ->join('users as owners', 'books.owner_id', '=', 'owners.id')
                    ->where('loans.borrower_id', auth()->id())
                    //->whereIn('status', ['returned'])
                    ->latest('borrowed_at')
                    ->selectRaw('owners.id, owners.name, COUNT(*) as total_books')
                    ->groupBy('owners.id', 'owners.name')
            )
            ->heading('Qui a emprunté mes livres ? 👥')
            ->description('Cette liste affiche les personnes qui ont emprunté vos livres.')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Emprunteurs')
                    ->wrap()
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('total_books')
                    ->label('Nombre de livres empruntés')
                    ->sortable(),
            ])
            ->recordUrl(null)
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'in_progress' => 'En cours',
                        'returned' => 'Terminé',
                    ])
                    ->default('in_progress')
            ]);
    }
} 