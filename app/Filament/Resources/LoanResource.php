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
use Filament\Forms\Components\Toggle;

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
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Enums\ActionsPosition;
use Filament\Support\Enums\ActionSize;
use Illuminate\Support\Facades\Auth;

// Widgets

class LoanResource extends Resource
{
    protected static ?string $model             = Loan::class;
    protected static ?string $modelLabel        = 'Emprunt';
    protected static ?string $pluralModelLabel  = 'Emprunts';
    protected static ?string $navigationGroup   = 'Gestion des prêts';
    protected static ?string $navigationIcon    = 'heroicon-o-shopping-bag';

    public static function getNavigationLabel(): string
    {
        return self::userHasAnyRole(['admin', 'super_admin']) 
            ? 'Emprunts' 
            : 'Mes emprunts';
    }

    // Removed redundant local counter. Counts are provided by LoanService.

    private static function userHasRole(string|array $roles): bool
    {
        $user = Auth::user();
        return $user instanceof \App\Models\User && method_exists($user, 'hasRole') && $user->hasRole($roles);
    }

    private static function userHasAnyRole(array $roles): bool
    {
        $user = Auth::user();
        return $user instanceof \App\Models\User && method_exists($user, 'hasAnyRole') && $user->hasAnyRole($roles);
    }

    private static function userCan(string $ability, mixed $arguments = null): bool
    {
        $user = Auth::user();
        return $user instanceof \App\Models\User && method_exists($user, 'can') && $user->can($ability, $arguments);
    }

    public static function getNavigationBadge(): ?string
    {
        $counts = app(LoanService::class)->getLoanCountsByStatus(
            self::userHasRole('user') ? Auth::id() : null
        );
        
        if(self::userHasAnyRole(['admin', 'super_admin'])){
    
            return implode(' - ', array_values($counts));
        }
        if(self::userHasRole('user')){
    
            return $counts['in_progress'];
        }
        return null;
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        if(self::userHasAnyRole(['admin', 'super_admin'])){
            return 'Nombre de prêts en cours - en attente - retournés';
        }

        return 'Nombre de prêts en cours';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('borrower_id')
                    ->label('Emprunteur')
                    ->relationship('borrower', 'name')
                    ->required(),
         
                Forms\Components\Select::make('book_id')
                    ->label('Ouvrage')
                    ->relationship('book', 'title')
                    ->searchable()
                    ->required(),

                Forms\Components\Select::make('status')
                    ->label('Statut')
                    ->options(Loan::getStatusLabelsForAdmin())
                    ->required(),
                
                Forms\Components\DatePicker::make('borrowed_at')
                    ->label('Date d\'emprunt')
                    ->displayFormat('D d/m/Y')
                    ->native(false)
                    ->nullable(true),
                
                Forms\Components\DatePicker::make('returned_at')
                    ->label('Date de retour effective')
                    ->displayFormat('D d/m/Y')
                    ->native(false)
                    ->nullable(true),

                Forms\Components\DatePicker::make('to_be_returned_at')
                    ->label('Date de retour programmée')
                    ->displayFormat('D d/m/Y')
                    ->native(false)
                    ->nullable(true),
                
                Forms\Components\DatePicker::make('return_signaled_at')
                    ->label('Date de signalement du retour')
                    ->displayFormat('D d/m/Y')
                    ->native(false)
                    ->nullable(true),

                Forms\Components\DatePicker::make('first_reminder_sent_at')
                    ->label('Rappel envoyé le')
                    ->displayFormat('D d/m/Y')
                    ->native(false)
                    ->nullable(true),
                
                Forms\Components\DatePicker::make('last_recurring_reminder_sent_at')
                    ->label('Rappel récurrent envoyé le')
                    ->displayFormat('D d/m/Y')
                    ->native(false)
                    ->nullable(true),
                
                Forms\Components\DatePicker::make('urgent_notification_sent_at')
                    ->label('Rappel urgent envoyé le')
                    ->displayFormat('D d/m/Y')
                    ->native(false)
                    ->nullable(true),     
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => 
                $query->when(
                    self::userHasRole('user'),
                    fn (Builder $query) => 
                    $query->where('borrower_id', Auth::id())
                )->orderByRaw("FIELD(status, 'overdue', 'return_in_progress', 'in_progress', 'returned') ASC")
                ->orderBy('to_be_returned_at', 'asc')
            )
            ->columns(self::getTableColumns())
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Statut')
                    ->options(Loan::getStatusLabelsForAdmin()),

                Tables\Filters\SelectFilter::make('borrower_id')
                    ->label('Emprunteur')
                    ->options(User::all()->pluck('name', 'id'))
            ])
            ->actions(
                ActionGroup::make(
                    self::getTableActions(),
                )
                ->label('Prolonger / Rendre')
                ->icon('heroicon-m-ellipsis-vertical')
                ->size(ActionSize::Small)
                ->color('primary')
                ->button()
            )
            
            ->bulkActions([
                self::getTableBulkActions(),
            ])
            ->groups([
                Group::make('status')
                ->label('Statut')
                ->getTitleFromRecordUsing(fn (Loan $record): string => 
                    $record->getStatusLabel()
                )
            ])
            ->actionsPosition(ActionsPosition::BeforeColumns)
            ->defaultGroup('status');
    }

    private static function getTableColumns(): array
    {
        $commonColumns = [
            // ajoute une colonne qui compte le nombre de jour de retard
            Tables\Columns\TextColumn::make('delay')
                ->label('Délai')
                ->width('15%')
                ->state(function (Loan $record): string {
                    return $record->getDelayMessage();
                })
                ->sortable(),

            Tables\Columns\TextColumn::make('to_be_returned_at')
                ->label('Date de retour')
                ->date('d/m/Y')
                ->width('15%')
                ->sortable(),                

            Tables\Columns\TextColumn::make('status')
                ->label('Statut')
                ->badge()
                ->color(fn (Loan $record): string => $record->getStatusColor())
                ->state(fn (Loan $record): string => $record->getStatusLabel())
                ->sortable(),

            Tables\Columns\TextColumn::make('borrower.email')
                ->label('Emprunteur')
                ->sortable()
                ->visible(fn (): bool => self::userHasRole('super_admin') || self::userHasRole('admin')),

            Tables\Columns\ImageColumn::make('book.cover_url')
                ->label('Couverture')
                ->sortable()
                ->defaultImageUrl(url('/storage/book-placeholder.jpeg'))
                ->height(75),

            Tables\Columns\TextColumn::make('book.title')
                ->label('Ouvrage')
                ->url(fn (Loan $record) 
                    => self::userHasRole('super_admin') || self::userHasRole('admin') ? 
                    route('filament.admin.resources.book-admins.edit', $record->book_id) : 
                    url('admin/books?tableSearch=' . $record->book->title))
                
                ->sortable()
                ->wrap()
                ->width('15%'),
        ];

        return $commonColumns;
    }

    private static function getTableActions(): array
    {
        return [
            Tables\Actions\EditAction::make(),
            Tables\Actions\Action::make('confirm_return')
                ->label('Valider le retour !!!!')
                ->icon('heroicon-s-check-circle')
                ->color('success')
                ->visible(fn (Loan $record) => 
                    self::userHasRole('super_admin'))
                ->action(fn (Loan $record) => 
                    app(LoanService::class)->validateReturn($record)
                ),
            Tables\Actions\Action::make('extend_loan')
                ->label('Prolonger le prêt')
                ->color('success')
                ->icon('heroicon-s-plus-circle')
                ->requiresConfirmation()
                ->modalDescription('Voulez-vous vraiment prolonger le prêt de ' . config('app.extend_loan_months') . ' mois ?')
                ->visible(fn (Loan $record) => 
                    $record->status === 'in_progress' && $record->extended_for === null
                )
                ->action(fn (Loan $record) => 
                    app(LoanService::class)->extendLoan($record)
                ),
            self::getValidateReturnAction(),
            self::getReturnAction(),
        ];
       
    }

    private static function getValidateReturnAction(): Tables\Actions\Action
    {
        return Tables\Actions\Action::make('validate_return')
            ->label('Valider le retour')
            ->visible(fn (Loan $record) => 
                self::userHasRole('super_admin') && 
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
                self::userCan('return', $record) && 
                ($record->status === Loan::STATUS_IN_PROGRESS || $record->status === Loan::STATUS_OVERDUE)
            );
    }

    private static function getTableBulkActions(): Tables\Actions\BulkActionGroup
    {
        return Tables\Actions\BulkActionGroup::make([
            Tables\Actions\DeleteBulkAction::make()
                ->visible(fn (): bool => self::userHasRole('super_admin') || self::userHasRole('admin')),
        ]);
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