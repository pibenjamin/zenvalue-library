<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LoanResource\Pages;
use App\Filament\Resources\LoanResource\RelationManagers;
use App\Models\Loan;
use App\Models\Book;
use App\Models\User;
use App\Services\LoanService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

use Filament\Tables\Filters\Filter;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\QueryBuilder;
use Filament\Tables\Filters\QueryBuilder\Constraints\NumberConstraint;
use Filament\Tables\Actions\ActionGroup;

class LoanResource extends Resource
{
    protected static ?string $model = Loan::class;
    protected static ?string $modelLabel = 'Prêt';
    protected static ?string $pluralModelLabel = 'Prêts';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Gestion des prêts';



    public static function getNavigationLabel(): string
    {
        if(in_array(auth()->user()->role->name, ['admin', 'super_admin'])){
            return 'Prêts';
        }
        if(in_array(auth()->user()->role->name, ['user'])){
            return 'Mes prêts';            
        }


    }

    public static function getNavigationBadge(): ?string
    {

        if(in_array(auth()->user()->role->name, ['admin', 'super_admin']))
        {
            $loansInProgress = static::getModel()::where('status', 'in_progress')->count();
                return $loansInProgress;
        }

        return null;
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return in_array(auth()->user()->role->name, ['admin', 'super_admin']) 
            ? 'Nombre de prêts en cours' 
            : null;
    }

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
        // L'utilisateur ne peut voir que ses propres emprunts
        if(auth()->user()->role->name === 'user') 
        {
            $table->modifyQueryUsing(function (Builder $query) { 
                if (auth()->user()->role->name === 'user') { 
                    return $query->where('borrower_id', auth()->id()); 
                } 
            }); 
        }

        $adminColumns = [
            Tables\Columns\TextColumn::make('return_confirmation_token')    
                ->label('Token')
        ];


        $commonColumns = [
            Tables\Columns\TextColumn::make('borrower.email')
            ->label('Emprunteur')
            ->sortable(),

            Tables\Columns\TextColumn::make('book.title')
                ->label('Ouvrage')
                ->sortable(),

            Tables\Columns\TextColumn::make('to_be_returned_at')
                ->label('Date de retour')
                ->date('d/m/Y')
                ->sortable(),

            Tables\Columns\TextColumn::make('status')
                ->label('Statut')
                ->badge()
                ->color(fn (Loan $record): string => $record->getStatusColor())
                ->state(function ($record): string {
                    $statusLabels = $record->getStatusLabels();
                    return $statusLabels[$record->status];
                })
                ->sortable(),
        ];

        if (auth()->user()?->hasAnyRole(['super_admin', 'admin'])) {
            $table->columns(array_merge($commonColumns, $adminColumns));
        }else{
            $table->columns($commonColumns);
        }


            $table->filters([
                
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'En attente',
                        'confirmed' => 'Confirmé',
                        'returned' => 'Retourné',
                    ]),




            ])
            ->actions([
                ActionGroup::make([
                    Tables\Actions\EditAction::make(),

                    Tables\Actions\Action::make('validate_return')
                        ->label('Valider le retour')
                        ->visible(fn (Loan $record) => auth()->user()?->hasRole('super_admin') && $record->status === 'in_progress')
                        ->action(function (Loan $record) {
                            app(LoanService::class)->validateReturn($record);
                        })
                        ->color('success')
                        ->requiresConfirmation(),
    
                    Tables\Actions\Action::make('return')
                    ->label('Rendre ce livre')
                    ->icon('heroicon-s-arrow-up-on-square')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Rendre ce livre')
                    ->modalDescription(fn (Loan $record) => "Voulez-vous rendre {$record->book->title} ?")
                    ->action(function (Loan $record) {
                        app(LoanService::class)->userSignaleReturn($record);
                        $record->refresh();
                    })
                    ->visible(fn (Loan $record) => auth()->user()?->can('return', $record) && $record->status === 'in_progress')

                ])
                


            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);

            return $table;
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
