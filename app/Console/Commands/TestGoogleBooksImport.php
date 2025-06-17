<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\GoogleBooksService;

class TestGoogleBooksImport extends Command
{
    protected $signature = 'books:test-google-import {isbn}';
    protected $description = 'Test l\'importation de données depuis Google Books';

    public function handle(GoogleBooksService $googleBooksService)
    {
        $isbn = $this->argument('isbn');
        $this->info("Recherche du livre avec l'ISBN : {$isbn}");

        try {
            $bookData = $googleBooksService->searchByIsbn($isbn);
            
            if (!$bookData) {
                $this->error("Aucune donnée trouvée pour l'ISBN : {$isbn}");
                return;
            }

            $this->info("\nDonnées récupérées :");
            
            // Afficher les données dans un tableau
            $rows = [];
            foreach ($bookData as $key => $value) {
                if (is_array($value)) {
                    $value = implode(', ', $value);
                }
                $rows[] = [
                    ucfirst(str_replace('_', ' ', $key)),
                    $value ?? 'Non disponible'
                ];
            }

            $this->table(
                ['Champ', 'Valeur'],
                $rows
            );

        } catch (\Exception $e) {
            $this->error("Erreur lors de l'importation : " . $e->getMessage());
        }
    }
} 