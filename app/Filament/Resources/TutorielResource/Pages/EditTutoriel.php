<?php

namespace App\Filament\Resources\TutorielResource\Pages;

use App\Filament\Resources\TutorielResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTutoriel extends EditRecord
{
    protected static string $resource = TutorielResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function afterValidate(): void
    {

        // fait moi un strip qui passe de ça 
        // <iframe title="Emprunter, Prolonger un prêt et signaler un retour de prêt" width="560" height="315" src="https://clip.place/videos/embed/tUnyDDeR7EYXtRHPgajfa9" frameborder="0" allowfullscreen="" sandbox="allow-same-origin allow-scripts allow-popups allow-forms"></iframe> à ça
        //    https://clip.place/w/tUnyDDeR7EYXtRHPgajfa9 


        // Extraire l'ID de la vidéo de l'URL iframe
        if (preg_match('/embed\/([\w-]+)/', $this->record->video_url, $matches)) {
            // Construire la nouvelle URL
            $this->record->video_url = 'https://clip.place/w/' . $matches[1];
        }
    }
}
