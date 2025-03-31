<?php

namespace App\Filament\Resources\BookResource\Widgets;

use App\Models\Book;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use App\Services\BookService;
use Filament\Notifications\Notification;
class ContributionWidget extends BaseWidget
{

    public ?Model $record                   = null;
    protected int|string|array $columnSpan  = 'full';
    protected static ?bool $collapsible     = true;
    protected static ?int $sort             = 1; // Position après le FilamentInfoWidget


    public function getHeading(): ?string
    {
        return 'Mes livres en cours de qualification';
    }


    public function table(Table $table): Table
    {
        return $table
            ->query(    
                Book::query()
                    ->where('owner_id', auth()->id())
                    ->where('status', Book::STATUS_TO_QUALIFY)
            )
            ->heading('Liste des livres en cours de dépot au bureau')
            
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Livre')
                    ->openUrlInNewTab()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('isbn')
                    ->label('ISBN')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\ImageColumn::make('cover_url')
                    ->label('Couverture')
                    ->height(50)
                    ->toggleable(),

            ])
            ->defaultPaginationPageOption(2)
            ->paginationPageOptions([2])
            ->actions([
                Tables\Actions\Action::make('confirm_dropoff')
                    ->label('J\'ai déposé le livre au bureau')
                    ->button()
                    ->color('success')
                    ->icon('heroicon-o-check-circle')
                    ->action(function (Book $record) {

                        app(BookService::class)->dropOffNotify($record);

                        $record->status = Book::STATUS_DROP_OFF;
                        $record->save();

                        Notification::make()
                            ->title('Le bibliothécaire a été notifié du dépot du livre')
                            ->send();
                    })
            ])
            ->filters([                   
            ]);
    }

    protected function getTablePollingInterval(): ?string
    {
        return null;
    }
}
