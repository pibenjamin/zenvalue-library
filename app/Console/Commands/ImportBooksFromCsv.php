<?php

namespace App\Console\Commands;

use App\Models\Book;
use Illuminate\Console\Command;

class ImportBooksFromCsv extends Command
{
    protected $signature = 'books:import-csv {file?}';
    protected $description = 'Import books from CSV file';

    public function handle()
    {
        $file = $this->argument('file') ?? public_path('storage/books_from_mobile_app.xlsx.csv');
        
        if (!file_exists($file)) {
            $this->error("Le fichier $file n'existe pas!");
            return 1;
        }

        $this->info("Début de l'importation depuis $file");
        
        $header = null;
        $data = [];
        
        if (($handle = fopen($file, 'r')) !== false) {
            while (($row = fgetcsv($handle, 1000, ',')) !== false) {
                if (!$header) {
                    $header = $row;
                    continue;
                }
                
                $row = array_combine($header, $row);
                
                try {
                    // Gestion de l'auteur
                    $authorName = $row['Auteurs'] ?? null;
                    $authorId = null;
                    
                    if ($authorName) {
                        // Recherche ou création de l'auteur
                        $author = \App\Models\Author::firstOrCreate(
                            ['name' => trim($authorName)],
                            ['name' => trim($authorName)]
                        );
                        $authorId = $author->id;
                    }

                    // Formatage de la date de publication
                    $publishedAt = null;
                    if (!empty($row['Date de publication'])) {
                        try {
                            // Tente d'abord le format complet dd/mm/yyyy
                            if (str_contains($row['Date de publication'], '/')) {
                                $date = \Carbon\Carbon::createFromFormat('d/m/Y', $row['Date de publication']);
                            } 
                            // Si c'est juste une année YYYY
                            else {
                                $year = trim($row['Date de publication']);
                                if (is_numeric($year) && strlen($year) === 4) {
                                    $date = \Carbon\Carbon::createFromDate($year, 1, 1);
                                } else {
                                    throw new \Exception("Format d'année invalide");
                                }
                            }
                            $publishedAt = $date->format('Y-m-d');
                        } catch (\Exception $e) {
                            $this->warn("Format de date invalide pour {$row['Titre']}: {$row['Date de publication']}");
                        }
                    }

                    // Formatage du chemin de la couverture
                    $coverPath = null;
                    if (!empty($row['Chemin de la couverture'])) {
                        $coverPath = str_replace('/MyLibrary/Images/Books/', 'books/covers/', $row['Chemin de la couverture']);
                    }

                    $bookData = [
                        'title'             => $row['Titre'] ?? null,
                        'author_id'         => $authorId,
                        'published_at'      => $publishedAt,
                        'publisher'         => $row['Editeur'] ?? null,
                        'pages'             => $row['Pages'] ?? null,
                        'isbn'              => $row['ISBN'] ?? null,
                        'description'       => $row['Résumé'] ?? null,
                        'cover_path'        => $coverPath,
                        'owner_id'          => 2,
                        'support_id'        => 1
                    ];




                    // Suppression du champ authors s'il existe encore dans les données
                    unset($bookData['authors']);

                    // Nettoyage des données nulles ou vides
                    $bookData = array_filter($bookData, function ($value) {
                        return $value !== null && $value !== '';
                    });

                    // Création ou mise à jour du livre
                    $book = Book::updateOrCreate(
                        ['isbn' => $bookData['isbn']], // Clé unique
                        $bookData
                    );

                    
                    $this->info("Livre importé: {$row['Titre']}");
                    
                } catch (\Exception $e) {
                    $this->error("Erreur lors de l'importation de {$row['Titre']}: " . $e->getMessage());
                }
            }
            fclose($handle);
        }

        $this->info('Importation terminée!');
        return 0;
    }
} 