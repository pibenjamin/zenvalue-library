<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use DOMDocument;
use DOMXPath;

class SearchOL extends Command
{
    protected $signature = 'book:search-ol {title}';
    protected $description = 'Recherche un livre par titre sur OpenLibrary';

    public function handle()
    {
        $title = $this->argument('title');
        $this->info("Recherche du livre avec le titre: {$title}");
        
        // Tableau pour stocker les résultats
        $results = [];

        try {
            $response = Http::get("https://openlibrary.org/search.json?title={$title}");
            
            if ($response->successful()) {
                $data = $response->json();

                if(count($data['docs']) ==  0){
                    $this->error("Livre non trouvé");
                }else{
                    $this->info(count($data['docs']) . " livre(s) trouvé(s)");
                }

                foreach($data['docs'] as $index => $doc){
                    // Collecter les informations pour chaque livre
                    $results[] = [
                        'Index' => $index + 1,
                        'Titre' => $doc['title'],
                        'Clé OL' => $doc['key'],
                        'Fichier' => Str::slugify($doc['title']).'.html'
                    ];

                    $this->info('recherche avec la clé: '.$doc['key']);

                    $pageContent =  file_get_contents("https://openlibrary.org{$doc['key']}");


                    $dom = new DOMDocument();
                    libxml_use_internal_errors(true);
                    $dom->loadHTML($pageContent);
                    libxml_clear_errors();


                    
                    $htmlString = $dom->saveHTML();

                    $slugTitle = Str::slugify($doc['title']);
                    // enregistrer le contenu de la page dans un fichier html
                    Storage::disk('local')->put($slugTitle.'.html', $htmlString);

                    $this->info('Livre enregistré avec succès');
                    $this->info('Lien: '.Storage::url($slugTitle.'.html'));


                    
                }

                // Afficher le tableau récapitulatif à la fin
                $this->info("\nRécapitulatif des livres traités :");
                $this->table(
                    ['#', 'Titre', 'Clé OpenLibrary', 'Fichier local'],
                    $results
                );

            } else {
                $this->error("Livre non trouvé pour le titre: {$title}");
            }
        } catch (\Exception $e) {
            $this->error("Erreur lors de la recherche: " . $e->getMessage());
        }
    }
} 