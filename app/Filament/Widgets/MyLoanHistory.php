<?php

namespace App\Filament\Widgets;

use App\Models\Loan;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;

class MyLoanHistory extends BaseWidget
{
    use HasWidgetShield;

    protected static ?int $sort = 2; // Position après le FilamentInfoWidget
    protected static ?int $defaultTableRecordsPerPage = 5;

    protected function getHeading(): ?string
    {
        return 'Mon historique de prêts';
    }
    

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Loan::query()
                    ->where('borrower_id', auth()->id())
                    ->whereIn('status', ['returned'])
                    ->latest('borrowed_at')
                    ->limit(3)
            )
            ->heading('Mon historique de prêts 📚')
            ->description('Cette liste affiche les livres que vous avez empruntés, si le prêt est terminé.')
            ->columns([
                Tables\Columns\TextColumn::make('book.title')
                    ->label('Livre')
                    ->wrap()
                    ->sortable(),

                    Tables\Columns\ImageColumn::make('book.cover_url')
                    ->label('Couverture')
                    ->sortable()
                    ->height(50),

                Tables\Columns\TextColumn::make('returned_at')
                    ->label('Retourné le')
                    ->dateTime('d/m/Y'),
            ])
            ->paginated([3, 'all']);
    }


} 