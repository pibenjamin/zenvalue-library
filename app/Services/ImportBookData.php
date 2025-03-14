<?php

namespace App\Services;

use App\Mail\LoanConfirmed; 
use App\Mail\UserSignaledReturn;
use App\Models\Book;
use App\Models\Author;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use App\Mail\AdminConfirmReturn;
use Illuminate\Support\Facades\Http;
use Symfony\Component\BrowserKit\HttpBrowser;
use Symfony\Component\HttpClient\HttpClient;
use Illuminate\Support\Facades\Storage;



class ImportBookData
{
    public function importFromCalPage(Book $book)
    {
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
                return;
            }

            // Sauvegarde du crawler original
            $authorCrawler = $crawler;

            // Première recherche (auteurs)
            $authorNodes = $authorCrawler->filterXPath('//div[contains(text(), "Auteur(s)")]/a');
            if ($authorNodes->count() > 0) {
                $authors = $authorNodes->each(function ($node) {
                    return $node->text();
                });
    
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
            }



            $pagesCrawler = $crawler;
            // Deuxième recherche (pages) - utilise le crawler original
            $pagesNode = $pagesCrawler->filterXPath('//div[@id="book-more-more-details-mobile-cont"]/div[@class="row"][2]');
            if ($pagesNode->count() > 0) {
                $htmlPagesNode = $pagesNode->html();
                if (preg_match('/(\d+)\s*pages/', $htmlPagesNode, $matches)) {
                    $pages = (int)$matches[1];
                    $book->pages = $pages;
                }
            }
            


            $isbnCrawler = $crawler;
            $isbnNode = $isbnCrawler->filterXPath('//div[@id="ean"]')->attr('data-ean');
            if (!$isbnNode) {
                Notification::make()
                    ->title('ISBN non trouvé')
                    ->body('L\'ISBN n\'a pas été trouvé pour le livre ' . $book->title)
                    ->danger()
                    ->send();
            }
            if($isbnNode) {
                $book->isbn = $isbnNode;
            }


            $dateCrawler = $crawler;
            $dateNode = $dateCrawler->filterXPath('//div[contains(text(), "Date de publication")]/text()');
            if ($dateNode->count() > 0) {
                if (preg_match('/(\d{4})/', $dateNode->text(), $matches)) {
                    $year = $matches[1];
                    $book->year_of_publication = $year;
                }

            }
            

            $dimensionsCrawler = $crawler;
            $dimensionsNode = $dimensionsCrawler->filterXPath('//div[@id="dimensions"]//p[contains(text(), "cm")]');
            if ($dimensionsNode->count() > 0) {
                $book->dimensions = $dimensionsNode->text();
            }

            $publishedCrawler = $crawler;
            $publishedNode = $publishedCrawler->filterXPath('//div[contains(text(), "Éditeur")]//a');
            if ($publishedNode->count() > 0) {
                $book->publisher = $publishedNode->text();
            }

            $titleCrawler = $crawler;
            $titleNode = $titleCrawler->filterXPath('//div[@id="book-title-and-details"]//h1');
            if ($titleNode->count() > 0) {
                $book->title = $titleNode->text();
            }

            $langCrawler = $crawler;
            $langNode = $langCrawler->filterXPath('//span[contains(text(), "Langue")]');
            if($langNode->count() > 0) {
            $lang = $langNode->ancestors()->filter('b')->first()->text();
            } else {
                $lang = null;
            }
            if($lang == 'Anglais') {
                $lang = 'en';   
            } 
            elseif($lang == 'Français') {
                $lang = 'fr';
            }  
            
            
            $book->lang = $lang;

            $coverCrawler = $crawler;
            $coverNode = $coverCrawler->filterXPath('//img[@id="book-cover"]')->attr('src');
            if (!$coverNode) {
                throw new \Exception('Image de couverture non trouvée');
            }

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


            $book->cover_url = $filename;

            //$book->cal_page = 'parsed';

            $book->slug = Str::slugify($book->title);

            $book->save();
    }
}