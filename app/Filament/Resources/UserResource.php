<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\Tag;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Toggle;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Tables\Enums\ActionsPosition;
use App\Filament\Actions\SaveAndNotifyActivation;

use Filament\Forms\Components\Actions\Action;


class UserResource extends Resource
{
    protected static ?string $model             = User::class;
    protected static ?string $modelLabel        = 'Utilisateur';
    protected static ?string $pluralModelLabel  = 'Utilisateurs';    
    protected static ?string $navigationGroup   = 'Gestion des utilisateurs';
    protected static ?string $navigationIcon    = 'heroicon-o-user-group';

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()->hasRole(['admin', 'super_admin']);
    }

    public static function getNavigationBadge(): ?string
    {
        $counts = User::where('is_activated', false)->count();
        
        if(auth()->user()?->hasAnyRole(['admin', 'super_admin'])){
    
            if($counts > 0){
                return $counts;
            }

            return '';
        }
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        if(auth()->user()?->hasAnyRole(['admin', 'super_admin'])){
            return 'Nombre d\'utilisateurs non activés';
        }
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

                Toggle::make('is_activated')
                    ->label('Activé')
                    ->default(false)
                    ->onColor('success')
                    ->offColor('danger'),

                Forms\Components\Select::make('roles')
                    ->label('Rôles')
                    ->multiple()
                    ->relationship('roles', 'name')
                    ->preload(),

                Forms\Components\FileUpload::make('avatar')
                    ->label('Avatar')
                    ->imageEditor()
                    ->directory('avatars')
                    ->disk('public')
                    ->maxSize(5120),

                Forms\Components\DatePicker::make('updated_at')
                    ->label('Modifié le')
                    ->displayFormat('d/m/Y')
                    ->locale('fr')
                    ->native(false)
                    ->required(),

                Forms\Components\DatePicker::make('created_at')
                    ->label('Créé le')
                    ->displayFormat('d/m/Y')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\ImageColumn::make('avatar')
                    ->sortable()
                    ->label('Avatar')
                    ->circular()
                    ->defaultImageUrl(url('/storage/avatars/default-avatar.png'))
                    ->height(50),

                    Tables\Columns\TextColumn::make('updated_at')
                    ->label('Modifié le')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),

                Tables\Columns\TextColumn::make('last_login_at')
                    ->label('Dernière connexion')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),


                Tables\Columns\TextColumn::make('roles.name')
                    ->label('Rôles')
                    ->searchable(),

                Tables\Columns\TextColumn::make('is_after_release')
                    ->label('Activé après la sortie')
                    ->state(function ($record): string {
                        return $record->password == $record->email.'598625' ? 'NON' : 'OUI';
                    })
                    ->badge()
                    ->sortable()
                    ->color(fn (string $state): string => match ($state) {
                        'OUI' => 'success',
                        'NON' => 'danger',
                    }),

                Tables\Columns\TextColumn::make('is_activated')
                    ->label('Activé')
                    ->badge()
                    ->state(function ($record): string {
                        return $record->is_activated ? 'OUI' : 'NON';
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'OUI' => 'success',
                        'NON' => 'danger',
                    }),

                Tables\Columns\TextColumn::make('password')
                    ->label('Mot de passe'),


            ])
            ->defaultPaginationPageOption(200)
            ->paginationPageOptions([200, 500, 1000])
            ->filters([
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),
            ])
            ->actionsPosition(ActionsPosition::BeforeColumns)

            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ])
            ]);
    }

    public static function getWidgets(): array
    {
        return [
            UserResource\Widgets\LatestConnectedUsers::class,
        ];
    }



    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'view' => Pages\ViewUser::route('/{record}'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
