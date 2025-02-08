<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class SearchIsbnOL extends Command
{
    protected $signature = 'book:search-isbn {isbn}';
    protected $description = 'Recherche un livre par ISBN sur OpenLibrary';

    public function handle()
    {
        $isbn = $this->argument('isbn');
        $this->info("Recherche du livre avec l'ISBN: {$isbn}");

        try {
            $response = Http::get("https://openlibrary.org/isbn/{$isbn}.json");
            
            if ($response->successful()) {
                $data = $response->json();
                $this->table(
                    ['Champ', 'Valeur'],
                    [
                        ['Titre', $data['title'] ?? 'N/A'],
                        ['Auteurs', isset($data['authors']) ? implode(', ', array_column($data['authors'], 'name')) : 'N/A'],
                        ['Date de publication', $data['publish_date'] ?? 'N/A'],
                        ['Éditeur', $data['publishers'][0] ?? 'N/A'],
                    ]
                );
            } else {
                $this->error("Livre non trouvé pour l'ISBN: {$isbn}");
            }
        } catch (\Exception $e) {
            $this->error("Erreur lors de la recherche: " . $e->getMessage());
        }
    }
} 