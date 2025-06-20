<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Symfony\Component\BrowserKit\HttpBrowser;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\DomCrawler\Crawler;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

use App\Models\Book;
use App\Models\Author;
use App\Models\AquisitionRequest;
use App\Services\GoogleBooksService;

class ImportWithIsbn extends Command
{
    protected $signature = 'app:import-with-isbn';
    protected $description = 'Importe les informations des livres via l\'ISBN';

    public function handle(GoogleBooksService $googleBooksService)
    {
        $books = Book::where('parsed', false)->whereNotNull('isbn')->get();

        $this->info('Nombre de livres à traiter : ' . $books->count());

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

        $aquisitionRequests = AquisitionRequest::where('link_to_book', '!=', 'parsed')->whereNotNull('isbn')->get();

        $this->info('Nombre de demandes d\'acquisition à traiter : ' . $aquisitionRequests->count());

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

