<?php

namespace App\Filament\Resources;

// Framework & Base
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

// Models
use App\Models\Loan;
use App\Models\Book;
use App\Models\User;

// Services
use App\Services\LoanService;

// Filament Base
use Filament\Resources\Resource;
use App\Filament\Resources\LoanResource\Pages;
use App\Filament\Resources\LoanResource\RelationManagers;

// Filament Forms
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\DatePicker;

// Filament Tables
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Grouping\Group;

// Filament Filters
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\QueryBuilder;
use Filament\Tables\Filters\QueryBuilder\Constraints\NumberConstraint;

// Filament Components
use Filament\Infolists\Components\Tabs;

class LoanResource extends Resource
{
    protected static ?string $model             = Loan::class;
    protected static ?string $modelLabel        = 'Prêt';
    protected static ?string $pluralModelLabel  = 'Prêts';
    protected static ?string $navigationIcon    = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup   = 'Gestion des prêts';

    public static function getNavigationLabel(): string
    {
        return auth()->user()?->hasAnyRole(['admin', 'super_admin']) 
            ? 'Prêts' 
            : 'Mes prêts';
    }

    private static function getLoanCountsByStatus(?int $borrowerId = null): array
    {
        $query = static::getModel()::query();
        
        if ($borrowerId) {
            $query->where('borrower_id', $borrowerId);
        }

        if(auth()->user()?->hasRole(['admin', 'super_admin'])){
            return [
                'in_progress' => (clone $query)->where('status', 'in_progress')->count(),
                'pending' => (clone $query)->where('status', 'pending')->count(),
                'returned' => (clone $query)->where('status', 'returned')->count(),
            ];
        }
    }

    public static function getNavigationBadge(): ?string
    {
        $counts = app(LoanService::class)->getLoanCountsByStatus(
            auth()->user()?->hasRole('user') ? auth()->id() : null
        );
        
        if(auth()->user()?->hasAnyRole(['admin', 'super_admin'])){
    
            return implode(' - ', array_values($counts));
        }
        if(auth()->user()?->hasRole(['user'])){
    
            return $counts['in_progress'];
        }

    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        if(auth()->user()?->hasAnyRole(['admin', 'super_admin'])){
            return 'Nombre de prêts en cours - en attente - retournés';
        }

        return 'Nombre de prêts en cours';
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
                Forms\Components\DatePicker::make('to_be_returned_at')
                    ->label('Date de retour')
                    ->required()

                    
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => 
                auth()->user()?->hasRole('user')
                    ? $query->where('borrower_id', auth()->id())
                    : $query
            )
            ->columns(self::getTableColumns())
            ->filters([
                self::getStatusFilter(),
            ])
            ->actions([
                self::getTableActions(),
            ])
            ->bulkActions([
                self::getTableBulkActions(),
            ])
            ->groups([
                self::getStatusGroup(),
            ])
            ->defaultGroup('status');
    }

    private static function getTableColumns(): array
    {
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
                ->state(fn (Loan $record): string => $record->getStatusLabel())
                ->sortable(),
        ];

        if (auth()->user()?->hasAnyRole(['super_admin', 'admin'])) {
            $commonColumns[] = Tables\Columns\TextColumn::make('return_confirmation_token')
                ->label('Token');
        }

        return $commonColumns;
    }

    private static function getStatusFilter(): Tables\Filters\SelectFilter
    {
        return Tables\Filters\SelectFilter::make('status')
            ->options([
                'pending' => 'En attente',
                'confirmed' => 'Confirmé',
                'returned' => 'Retourné',
            ]);
    }

    private static function getTableActions(): ActionGroup
    {
        return ActionGroup::make([
            Tables\Actions\EditAction::make(),
            self::getValidateReturnAction(),
            self::getReturnAction(),
        ]);
    }

    private static function getValidateReturnAction(): Tables\Actions\Action
    {
        return Tables\Actions\Action::make('validate_return')
            ->label('Valider le retour')
            ->visible(fn (Loan $record) => 
                auth()->user()?->hasRole('super_admin') && 
                $record->status === 'in_progress'
            )
            ->action(fn (Loan $record) => 
                app(LoanService::class)->validateReturn($record)
            )
            ->color('success')
            ->requiresConfirmation();
    }

    private static function getReturnAction(): Tables\Actions\Action
    {
        return Tables\Actions\Action::make('return')
            ->label('Rendre ce livre')
            ->icon('heroicon-s-arrow-up-on-square')
            ->color('success')
            ->requiresConfirmation()
            ->modalHeading('Rendre ce livre')
            ->modalDescription(fn (Loan $record) => 
                "Voulez-vous rendre {$record->book->title} ?"
            )
            ->action(function (Loan $record) {
                app(LoanService::class)->userSignaleReturn($record);
                $record->refresh();
            })
            ->visible(fn (Loan $record) => 
                auth()->user()?->can('return', $record) && 
                $record->status === 'in_progress'
            );
    }

    private static function getTableBulkActions(): Tables\Actions\BulkActionGroup
    {
        return Tables\Actions\BulkActionGroup::make([
            Tables\Actions\DeleteBulkAction::make(),
        ]);
    }

    private static function getStatusGroup(): Group
    {
        return Group::make('status')
            ->label('Statut')
            ->getTitleFromRecordUsing(fn (Loan $record): string => 
                $record->getStatusLabel()
            );
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