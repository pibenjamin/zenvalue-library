<?php

namespace App\Filament\Resources\ParcoursResource\Pages;

use App\Filament\Resources\ParcoursResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Forms; 
use App\Models\Book;
use Filament\Notifications\Notification;
use App\Filament\Resources\ParcoursResource\Widgets\BookParcoursWidget;

class EditParcours extends EditRecord
{
    protected static string $resource = ParcoursResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\Action::make('addBooks')
            ->label('Ajouter des livres')
            ->icon('heroicon-o-book-open')
            ->modalHeading('Ajouter des livres')
            ->modalDescription('Ajouter des livres à ce parcours')
            ->form([
                Forms\Components\Select::make('books')
                    ->label('Livres')
                    ->multiple()
                    ->options(Book::all()->pluck('title', 'id'))
            ])
            ->action(function (array $data) {
                $parcours = $this->record;
                $parcours->books()->attach($data['books']);

                Notification::make()
                    ->title('Livres ajoutés')
                    ->body('Les livres ont été ajoutés au parcours')
                    ->success()
                    ->send();
            })
        ];
    }

    protected function getFooterWidgets(): array
    {
        return [
            BookParcoursWidget::class,
        ];
    }
}