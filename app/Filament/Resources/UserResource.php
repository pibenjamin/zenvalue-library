<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Select;


class UserResource extends Resource
{
    protected static ?string $model = User::class;
    protected static ?string $modelLabel = 'Utilisateur';
    protected static ?string $pluralModelLabel = 'Utilisateurs';    
    protected static ?string $navigationIcon = 'heroicon-o-user';

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()->hasRole(['admin', 'super_admin']);
    }


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Nom')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('email')
                    ->label('Email')
                    ->email()
                    ->required()
                    ->maxLength(255),
//                Forms\Components\Select::make('roles')
//                    ->label('Rôles')
//                    ->multiple()
//                    ->relationship('roles', 'display_name')
//                    ->preload()
//                    ->createOptionForm([
//                        Forms\Components\TextInput::make('name')
//                            ->label('Nom')
//                            ->required(),
//                    ]),
                Forms\Components\Select::make('roles')
                ->label('Rôles')
                ->multiple()
                ->relationship('roles', 'name')
                ->preload()

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Modifié le')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('roles.name')
                ->label('Rôles')
                ->searchable(),
                Tables\Columns\TextColumn::make('is_after_release')
                    ->label('Activé après la sortie')
                    ->state(function ($record): string {
                        return $record->updated_at > env('APP_RELEASE_DATE') ? 'OK' : 'NON';
                    })
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'OK' => 'success',
                        'NON' => 'danger',
                    }),
                



                
            ])
            ->defaultPaginationPageOption(200)
            ->paginationPageOptions([200, 500, 1000])
            ->filters([
            ])
            ->actions([
//                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ])



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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
