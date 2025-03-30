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
use App\Services\ImportBookData;
class SearchCal extends Command
{
    protected $signature = 'app:search-isbn-cal';
    protected $description = 'Recherche un livre par ISBN sur https://www.chasse-aux-livres.fr';

    public function handle()
    {
        $books = Book::whereNotNull('cal_page')->where('cal_page', '!=', 'parsed')->get();

        $this->info('Nombre de livres à traiter : ' . $books->count());

        foreach ($books as $book) {
            $this->info($book->cal_page);

            $importBookData = new ImportBookData();
            $importBookData->importFromCalPage($book);

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
                    ['Auteurs', $book->authors->pluck('name')->implode(', ')],
                ]
            );
        }
    }
} 

