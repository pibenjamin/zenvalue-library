<?php

namespace App\Console\Commands;

use App\Models\AquisitionRequest;
use App\Models\Book;
use App\Services\GoogleBooksService;
use Illuminate\Console\Command;

class ImportWithIsbn extends Command
{
    protected $signature = 'app:import-with-isbn {--force : Force la réimportation même pour les livres déjà parsés}';

    protected $description = 'Importe les informations des livres via l\'ISBN';

    public function handle(GoogleBooksService $googleBooksService)
    {
        $booksQuery = Book::whereNotNull('isbn');
        if (! $this->option('force')) {
            $booksQuery->where('parsed', false);
        }
        $books = $booksQuery->get();

        $this->info('Nombre de livres à traiter : '.$books->count());

        foreach ($books as $book) {
            $this->info($book->isbn);

            $success = $googleBooksService->importBookData($book);

            // afficher le livre
            $this->table(
                ['Champ', 'Valeur'],
                [
                    ['Titre', $book->title],
                    ['Couverture', $book->cover_url],
                    ['Pages', $book->pages],
                    ['Année', $book->year_of_publication],
                    ['Langue', $book->lang],
                    ['Dimensions', $book->dimensions],
                    ['Éditeur', $book->publisher],
                    ['ISBN', $book->isbn],
                    ['Auteurs', $book->authors->pluck('name')->implode(', ')],
                ]
            );
        }

        $aquisitionQuery = AquisitionRequest::whereNotNull('isbn');
        if (! $this->option('force')) {
            $aquisitionQuery->where('link_to_book', '!=', 'parsed');
        }
        $aquisitionRequests = $aquisitionQuery->get();

        $this->info('Nombre de demandes d\'acquisition à traiter : '.$aquisitionRequests->count());

        foreach ($aquisitionRequests as $aquisitionRequest) {
            $this->info($aquisitionRequest->isbn);

            // Ici, on ne peut importer que si l'ISBN est présent
            $book = Book::where('isbn', $aquisitionRequest->isbn)->first();
            if ($book) {
                $success = $googleBooksService->importBookData($book);
            }

            // afficher le livre
            $this->table(
                ['Champ', 'Valeur'],
                [
                    ['Titre', $aquisitionRequest->title],
                    ['ISBN', $aquisitionRequest->isbn],
                ]
            );
        }
    }
}
