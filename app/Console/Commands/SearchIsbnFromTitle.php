<?php

namespace App\Console\Commands;

use App\Models\Book;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class SearchIsbnFromTitle extends Command
{
    protected $signature    = 'book:search-isbn-from-title';
    protected $description  = 'Recherche les ISBN sur OpenLibrary pour les livres sans ISBN';

    public function handle()
    {
        $books = Book::whereNull('isbn')->get();
        $bar = $this->output->createProgressBar(count($books));
        
        $this->info("Début de la recherche pour " . count($books) . " livres");
        $bar->start();

        foreach ($books as $book) {
            try {
                $response = Http::get('https://openlibrary.org/search.json&fields=isbn', [
                    'q' => $book->title,
                    'limit' => 1
                ]);


                if ($response->successful() && !empty($response->json()['docs'])) {
                    $result = $response->json()['docs'][0];
                    if (isset($result['isbn'])) {
                        $isbn = is_array($result['isbn']) ? $result['isbn'][0] : $result['isbn'];
//                        $book->update(['isbn' => $isbn]);
                    $this->line("\ID BOOK: '{$book->id}'");
                    $this->line("\nISBN trouvé pour '{$book->title}': {$isbn}");
}
                }

                // Petit délai pour ne pas surcharger l'API
                sleep(1);
            } catch (\Exception $e) {
                $this->error("\nErreur pour '{$book->title}': " . $e->getMessage());
            }

            $bar->advance();
        }

        $bar->finish();
        $this->info("\nRecherche terminée!");
    }
} 