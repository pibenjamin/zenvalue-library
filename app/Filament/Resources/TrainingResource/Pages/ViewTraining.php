<?php

namespace App\Filament\Resources\TrainingResource\Pages;

use App\Filament\Resources\TrainingResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use App\Filament\Resources\TrainingResource\Widgets\BookTrainingsWidget;
use Filament\Forms;
use App\Models\Book;
use Filament\Notifications\Notification;
class ViewTraining extends ViewRecord
{
    protected static string $resource = TrainingResource::class;


    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('linkBook')
                ->label('Lier un livre')
                ->icon('heroicon-o-book-open')
                ->modalHeading('Lier un livre')
                ->modalDescription('Lier un livre à la formation')
                ->form([
                    Forms\Components\Select::make('book')
                        ->label('Livre')
                        ->options(Book::all()->pluck('title', 'id'))
                        ->searchable()
                        ->required(),
                ])
                ->action(function ($data) {

                    $this->record->books()->attach($data['book']);
                    $this->record->save();

                    Notification::make()
                        ->title('Livre lié avec succès')
                        ->success()
                        ->send();
                })
                ->visible(fn () => auth()->user()->hasRole('super_admin')),
        ];
    }

    protected function getFooterWidgets(): array
    {
        return [
            BookTrainingsWidget::class,
        ];
    }

}
