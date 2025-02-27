<?php

namespace App\Filament\Resources\BookAdminResource\Pages;

use App\Filament\Resources\BookAdminResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use App\Models\Book;
use App\Services\OpenLibraryService;    
use Illuminate\View\View;
use Illuminate\Support\Facades\Notification;

class EditBookAdmin extends EditRecord
{
    protected static string $resource = BookAdminResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\Action::make('CheckOpenLibrary')
                ->label('Vérifier les données d\'Open Library')
                ->icon('heroicon-o-check-circle')
                ->modalContent(fn (Book $record): View => view(
                    'filament.modals.view.compare_with_ol',
                    ['record' => $record, 'ol_data' => app(OpenLibraryService::class)->extractBookData($record->slug)],
                ))
        ];
    }

    public function updateField(string $field, string $value): void
    {
        $record = $this->getRecord();
        $record->$field = $value;
        $record->save();
        
        $this->dispatch('close-modal', id: 'compare_with_ol');
    }
}
