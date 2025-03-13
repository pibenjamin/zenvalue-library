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

            $browser = new HttpBrowser(HttpClient::create([
                'verify_peer' => false,
                'verify_host' => false,
                'headers' => [
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/91.0.4472.124',
                    'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
                    'Accept-Language' => 'fr,fr-FR;q=0.8,en-US;q=0.5,en;q=0.3',
                ]
            ]));
            
            $crawler = $browser->request('GET', $book->cal_page);
            $content = $browser->getResponse()->getContent();

            if (empty($content)) {
                $this->error('No content received from the page');
                return;
            }

            // Sauvegarde du crawler original
            $authorCrawler = $crawler;

            // Première recherche (auteurs)
            $authorNodes = $authorCrawler->filterXPath('//div[contains(text(), "Auteur(s)")]/a');
            $authors = $authorNodes->each(function ($node) {
                return $node->text();
            });
            $this->info("Auteurs trouvés : " . implode(', ', $authors));

            foreach($authors as $author) {
                $author = trim($author);
                if(!$Author = Author::where('name', $author)->first()) {
                    $Author = Author::create([
                        'name' => $author,
                    ]);
                }
                if (!$book->authors()->where('authors.id', $Author->id)->exists()) {
                    $book->authors()->attach($Author);
                }
            }

            $pagesCrawler = $crawler;
            // Deuxième recherche (pages) - utilise le crawler original
            $pagesNode = $pagesCrawler->filterXPath('//div[@id="book-more-more-details-mobile-cont"]/div[@class="row"][2]');
            
            $htmlPagesNode = $pagesNode->html();
            if (preg_match('/(\d+)\s*pages/', $htmlPagesNode, $matches)) {
                $pages = (int)$matches[1];
                $this->info("Nombre de pages : " . $pages);
                $book->pages = $pages;
            } else {
                $this->warn("Nombre de pages non trouvé");
            }

            $dateCrawler = $crawler;
            $dateNode = $dateCrawler->filterXPath('//div[contains(text(), "Date de publication")]/text()')->text();
            
            if (preg_match('/(\d{4})/', $dateNode, $matches)) {
                $year = $matches[1];
                $this->info("Année de publication : " . $year);
                $book->year_of_publication = $year;
            } else {
                $this->warn("Année de publication non trouvée");
            }

            $dimensionsCrawler = $crawler;
            $dimensionsNode = $dimensionsCrawler->filterXPath('//div[@id="dimensions"]//p[contains(text(), "cm")]')->text();
            $this->info("Dimensions : " . $dimensionsNode);
            $book->dimensions = $dimensionsNode;

            $publishedCrawler = $crawler;
            $publishedNode = $publishedCrawler->filterXPath('//div[contains(text(), "Éditeur")]//a')->text();
            $this->info("Éditeur : " . $publishedNode);
            $book->publisher = $publishedNode;

            $titleCrawler = $crawler;
            $titleNode = $titleCrawler->filterXPath('//div[@id="book-title-and-details"]//h1')->text();
            $this->info("Titre : " . $titleNode);
            $book->title = $titleNode;

            $coverCrawler = $crawler;
            $coverNode = $coverCrawler->filterXPath('//img[@id="book-cover"]')->attr('src');
            $this->info("Couverture : " . $coverNode);


            $langCrawler = $crawler;
            $langNode = $langCrawler->filterXPath('//span[contains(text(), "Langue")]');
            $lang = $langNode->ancestors()->filter('b')->first()->text();

            if($lang == 'Anglais') {
                $lang = 'en';   
            } 
            elseif($lang == 'Français') {
                $lang = 'fr';
            }  
            
            $this->info("Langue : " . $lang);
            
            $book->lang = $lang;

            $response = Http::withOptions([
                'verify' => false,
            ])->get($coverNode);
    
            if (!$response->successful()) {
                throw new \Exception('Failed to download image');
            }
    
            $image = $response->body();
    
            // Générer un nom de fichier unique
            $filename = 'books/covers/' . (string) Str::uuid() . '.jpg';
    
            // Sauvegarder dans le storage
            $fileSaved = Storage::disk('public')->put(
                '/' . $filename,
                $image
            );
            if($fileSaved) {
                $this->info("Fichier sauvegardé : " . $fileSaved);
            } else {
                $this->error("Erreur lors de la sauvegarde du fichier");
            }

            $book->cover_url = $filename;

            $book->cal_page = 'parsed';

            $book->slug = Str::slugify($book->title);

            $book->save();

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

