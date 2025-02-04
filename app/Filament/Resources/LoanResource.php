<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LoanResource\Pages;
use App\Filament\Resources\LoanResource\RelationManagers;
use App\Models\Loan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class LoanResource extends Resource
{
    protected static ?string $model = Loan::class;
    protected static ?string $modelLabel = 'Prêt';
    protected static ?string $pluralModelLabel = 'Prêts';
    

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('borrower_id')
                    ->relationship('borrower', 'name')
                    ->required(),
                Forms\Components\Select::make('book_id')
                    ->relationship('book', 'title')
                    ->required(),
                    
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('borrower.email')
                    ->label('Emprunteur')
                    ->sortable(),
                Tables\Columns\TextColumn::make('book.title')
                    ->label('Ouvrage')
                    ->sortable(),


                Tables\Columns\TextColumn::make('returned_at')
                    ->label('Date de retour')
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->label('Statut')
                    ->sortable(),

                Tables\Columns\TextColumn::make('return_confirmation_token')    
                    ->label('Token de confirmation')
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'En attente',
                        'confirmed' => 'Confirmé',
                        'returned' => 'Retourné',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLoans::route('/'),
            'create' => Pages\CreateLoan::route('/create'),
            'edit' => Pages\EditLoan::route('/{record}/edit'),
        ];
    }
}
