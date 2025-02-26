<?php

namespace App\Filament\Widgets;

use App\Models\Loan;
use App\Models\User;
use App\Models\Book;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class WhoBorrowedMyBooks extends BaseWidget
{
    protected int | string | array $columnSpan = 'full';

    protected static ?int $sort = 2;

    public static function canView(): bool
    {

        // if current user is a book owner
        // and the book has been borrowed at least once
        // then return true

        $ownerOfBooks = Book::where('owner_id', auth()->id())->get();

        foreach ($ownerOfBooks as $book) {

            $loans = Loan::where('book_id', $book->id)->get();

            if($loans->count() > 0) {
                return true;
            }


        }

        return false;

    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Loan::query()
                    ->join('books', 'loans.book_id', '=', 'books.id')
                    ->join('users as borrowers', 'loans.borrower_id', '=', 'borrowers.id')
                    ->where('books.owner_id', auth()->id())
                    //->whereIn('status', ['returned'])
                    ->latest('borrowed_at')
                    ->selectRaw('borrowers.id, borrowers.name, COUNT(*) as total_books')
                    ->groupBy('borrowers.id', 'borrowers.name')
            )
            ->heading('Qui est intéressé par mes livres ? 👥')
            ->description('Cette liste affiche les personnes qui empruntent ou ont emprunté vos livres.')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Emprunteur')
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
            ]);
    }
} 