<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ClaimResource\Pages;
use App\Filament\Resources\ClaimResource\RelationManagers;
use App\Models\Claim;
use App\Models\Book;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Notifications\Notification;
class ClaimResource extends Resource
{
    protected static ?string $model = Claim::class;

    protected static ?string $navigationIcon = 'heroicon-o-hand-raised';

    protected static ?string $navigationGroup = 'Gestion du catalogue';

    protected static ?int $navigationSort           = 5;


    protected static ?string $navigationLabel = 'Requêtes de propriété';
    public static function getNavigationBadge(): ?string
    {
        return Claim::where('status', 'pending')->count();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('book.title')
                    ->url(fn (Claim $record) => route('filament.admin.resources.books.edit', $record->book_id))
                    ->openUrlInNewTab(),
                Tables\Columns\TextColumn::make('user.name'),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'approved' => 'success',
                        'rejected' => 'danger',
                    }),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),    
                Tables\Actions\Action::make('approve')
                    ->label('Approuver')
                    ->color('success')
                    ->icon('heroicon-s-check')
                    ->action(function (Claim $record) {

                        $book = Book::find($record->book_id);
                        $book->owner_id = $record->user_id;
                        $book->save();

                        $record->status = 'approved';
                        $record->save();

                        Notification::make()
                        ->title('La propriété du livre a été transférée')
                        ->success()
                        ->send();

                        
                    })
                    ->visible(fn (Claim $record) => $record->status === 'pending' && auth()->user()->can('update', $record)), 
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
            'index' => Pages\ListClaims::route('/'),
            'create' => Pages\CreateClaim::route('/create'),
            'edit' => Pages\EditClaim::route('/{record}/edit'),
        ];
    }
}
