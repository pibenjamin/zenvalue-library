<?php

namespace App\Filament\Resources\BookAdminResource\Pages;

use App\Filament\Resources\BookAdminResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use App\Models\Book;
use App\Models\Author;
use App\Services\OpenLibraryService;    
use Illuminate\View\View;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;

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
                    ['record' => $record,
                     'ol_data' => app(OpenLibraryService::class)->extractBookDataFromOLKey($record->ol_key)],
                ))
                ->modalSubmitActionLabel('Valider et ajouter au catalogue')
                ->modalCancelActionLabel('Fermer')
                ->action(function (Book $record) {

                    $record->status = Book::STATUS_CONTRIBUTION_QUALIFIED;
                    $record->save();

                    Notification::make()
                        ->title('Données mises à jour')
                        ->success()
                        ->send();
                }),
        ];
    }

    public function updateField(string $field, string $value): void
    {
        $record = $this->getRecord();
        $record->$field = $value;
        $record->save();
        
        if($field == 'author') {

            if(preg_match('/,/', $value)) {
                $authors = explode(', ', $value);
            } else {
                $authors = [$value];
            }

            foreach($authors as $author) {
                $authorId = Author::createOrFirst([
                    'name' => $author,
                ])->id;
    
                $record->authors()->attach($authorId);
            }
        }

       $this->dispatch('close-modal', id: 'compare_with_ol');
    }

    public function saveCoverUrl(string $field, string $coverUrl): void
    {
        $record = $this->getRecord();
        // Ajouter le protocole si nécessaire
        $url = 'https://covers.openlibrary.org/b/isbn/'.$record->isbn.'-L.jpg';         
        // Télécharger l'image avec HTTP Client de Laravel
        $response = Http::withOptions([
            'verify' => false,
        ])->get($url);

        if (!$response->successful()) {
            throw new \Exception('Failed to download image');
        }

        $image = $response->body();

        // Générer un nom de fichier unique
        $filename = 'books/covers/' . (string) Str::uuid() . '.jpg';

            // Sauvegarder dans le storage
            Storage::disk('public')->put(
                '/' . $filename,
                $image
            );
            
            // Mettre à jour le record
            $record->cover_url = $filename;
            $record->save();
            
            Notification::make()
                ->title('Image de couverture mise à jour')
                ->success()
                ->send();

    }
}