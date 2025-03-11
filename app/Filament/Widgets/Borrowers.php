<?php

namespace App\Filament\Widgets;

use App\Models\Loan;
use App\Models\User;
use App\Models\Book;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Str;
class Borrowers extends BaseWidget
{
    use HasWidgetShield;

    protected int|string|array $columnSpan = 'full';
    protected static ?string $title = 'Ils empruntent ou ont emprunté vos livres';
    protected static ?bool $collapsible = true;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Loan::query()
                    ->select('loans.*')
                    ->join('books', 'loans.book_id', '=', 'books.id')
                    ->where('books.owner_id', auth()->id())
            )
            ->heading('Ils empruntent ou ont emprunté vos livres')
            ->columns([
                Tables\Columns\TextColumn::make('borrower.name')
                    ->label('Emprunteur')
                    ->sortable(),
                Tables\Columns\ImageColumn::make('borrower.avatar')
                    ->label('Photo')
                    ->circular()
                    ->url(fn (Loan $record): string => $record->borrower->avatar ? $record->borrower->avatar : url('/avatar/default-avatar.png'))
                    ->height(50),                    
                Tables\Columns\TextColumn::make('book.title')
                    ->label('Livre')
                    ->state(function (Loan $record): string {
                        return Str::words($record->book->title, 6, '…');
                    })
                    ->tooltip(fn (Loan $record): string => $record->book->title)
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Statut')
                    ->badge()
                    ->color(fn (Loan $record): string => $record->getStatusColor())
                    ->state(fn (Loan $record): string => $record->getStatusLabel())
                    ->sortable(),
            ])
            ->filters([                   
                Tables\Filters\SelectFilter::make('loans.borrower_id')
                    ->label('Emprunteur')
                    ->options(User::hasBorrowedFromUser(auth()->id())->pluck('name', 'id')),

            ]);
            
    }
}
