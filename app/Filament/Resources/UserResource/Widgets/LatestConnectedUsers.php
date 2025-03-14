<?php

namespace App\Filament\Resources\UserResource\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use App\Models\User;
use Filament\Tables\Columns\TextColumn;
use Malzariey\FilamentDaterangepickerFilter\Filters\DateRangeFilter;

use Carbon\Carbon;

class LatestConnectedUsers extends BaseWidget
{
    protected static ?string $heading = 'Derniers utilisateurs connectés';

    protected int|string|array $columnSpan = 'full';


    public function table(Table $table): Table
    {
        return $table
            ->query(
                User::query()->orderBy('last_login_at', 'desc')->limit(5)
            )
            ->columns([
                TextColumn::make('name'),
                Tables\Columns\ImageColumn::make('avatar')
                    ->sortable()
                    ->label('Avatar')
                    ->circular()
                    ->defaultImageUrl(url('/storage/avatars/default-avatar.png'))
                    ->height(50),
                TextColumn::make('email')
                ->label('Courriel'),

                TextColumn::make('last_login_at')->dateTime()
                ->label('Dernière connexion')
                ->sortable(),
            ])
            ->paginated(true)
            ->defaultPaginationPageOption(5)
            ->filters([
                DateRangeFilter::make('last_login_at')
                ->startDate(Carbon::now())
                ->endDate(Carbon::now())
                ->defaultLast30Days()
                ->label('Plage de dates')
            ]);
    }
}
