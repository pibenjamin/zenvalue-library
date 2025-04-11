<?php

namespace App\Filament\Resources\TrainingResource\Pages;

use App\Filament\Resources\TrainingResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\TrainingResource\Widgets\BookTrainingsWidget;
use Illuminate\Support\Str;
use Filament\Forms;
use App\Models\Book;

class EditTraining extends EditRecord
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
