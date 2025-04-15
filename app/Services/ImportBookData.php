<?php

namespace App\Services;

use App\Mail\LoanConfirmed; 
use App\Mail\UserSignaledReturn;
use App\Models\Book;
use App\Models\Author;
use App\Models\AquisitionRequest;
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
use Illuminate\Database\Eloquent\Model;


class ImportBookData
{
    public function importFromCalPage(Model $model)
    {

        if($model instanceof Book) {

            $book = $model;

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
                $message = 'Le contenu de la page n\'a pas été trouvé pour le livre ' . $book->title;
                Notification::make()
                    ->title('Contenu de la page non trouvé')
                    ->body($message)
                    ->danger()
                    ->send();
                Log::error($message);
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
            else {
                $message = 'Les auteurs n\'ont pas été trouvés pour le livre ' . $book->title;
                Notification::make()
                    ->title('Auteurs non trouvés')
                    ->body($message)
                    ->danger()
                    ->send();
                Log::error($message);
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
            else {
                $message = 'Le nombre de pages n\'a pas été trouvé pour le livre ' . $book->title;
                Notification::make()
                    ->title('Nombre de pages non trouvé')
                    ->body($message)
                    ->danger()
                    ->send();
                Log::error($message);
            }

            $isbnCrawler = $crawler;
            $isbnNode = $isbnCrawler->filterXPath('//div[@id="ean"]');



            if($isbnNode->count() > 0) {
                if($isbnNode->attr('data-ean')) {
                    $book->isbn = $isbnNode->attr('data-ean');
                }
                else {
                    $message = 'L\'ISBN n\'a pas été trouvé pour le livre ' . $book->title;
                    Notification::make()
                        ->title('ISBN non trouvé');
                }
            }
            else {
                $message = 'L\'ISBN n\'a pas été trouvé pour le livre ' . $book->title;
                Notification::make()
                    ->title('ISBN non trouvé')
                    ->body($message)
                    ->danger()
                    ->send();
                    Log::error($message);

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
            else {
                $message = 'Les dimensions n\'ont pas été trouvées pour le livre ' . $book->title;
                Notification::make()
                    ->title('Dimensions non trouvées')
                    ->body($message)
                    ->danger()
                    ->send();
                Log::error($message);
            }

            $publishedCrawler = $crawler;
            $publishedNode = $publishedCrawler->filterXPath('//div[contains(text(), "Éditeur")]//a');
            if ($publishedNode->count() > 0) {
                $book->publisher = $publishedNode->text();
            }
            else {
                $message = 'L\'éditeur n\'a pas été trouvé pour le livre ' . $book->title;
                Notification::make()
                    ->title('Éditeur non trouvé')
                    ->body($message)
                    ->danger()
                    ->send();
                Log::error($message);
            }

            $titleCrawler = $crawler;
            $titleNode = $titleCrawler->filterXPath('//div[@id="book-title-and-details"]//h1');
            if ($titleNode->count() > 0) {
                $book->title = $titleNode->text();
            }
            else {
                $message = 'Le titre n\'a pas été trouvé pour le livre ' . $book->title;
                Notification::make()
                    ->title('Titre non trouvé')
                    ->body($message)
                    ->danger()
                    ->send();
                Log::error($message);
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
            else {
                $message = 'La langue n\'a pas été trouvée pour le livre ' . $book->title;
                Notification::make()
                    ->title('Langue non trouvée')
                    ->body($message)
                    ->danger()
                    ->send();
                Log::error($message);
            }

            $book->lang = $lang;

            $coverCrawler = $crawler;
            $coverNode = $coverCrawler->filterXPath('//img[@id="book-cover"]')->attr('src');
            if (!$coverNode) {
                $message = 'L\'image de couverture n\'a pas été trouvée pour le livre ' . $book->title;
                Notification::make()
                    ->title('Image de couverture non trouvée')
                    ->body($message)
                    ->danger()
                    ->send();
                Log::error($message);
            }

            $response = Http::withOptions([
                'verify' => false,
            ])->get($coverNode);
    
            if (!$response->successful()) {
                $message = 'Failed to download image for book ' . $book->title;
                Notification::make()
                    ->title('Image de couverture non téléchargée')
                    ->body($message)
                    ->danger()
                    ->send();
                Log::error($message);
            }
    
            $image = $response->body();
    
            // Générer un nom de fichier unique
            $filename = 'books/covers/' . (string) Str::uuid() . '.jpg';
    
            // Sauvegarder dans le storage
            $fileSaved = Storage::disk('public')->put(
                '/' . $filename,
                $image
            );

            if (!$fileSaved) {
                $message = 'L\'image de couverture n\'a pas été téléchargée pour le livre ' . $book->title;
                Notification::make()
                    ->title('Image de couverture non téléchargée')
                    ->body($message)
                    ->danger()
                    ->send();
                Log::error($message);
            }
            
            $book->cover_url = $filename;

            $book->status = Book::STATUS_TO_QUALIFY;

            $book->cal_page = 'parsed';

            $book->slug = Str::slugify($book->title);

            $book->save();
        }

        if($model instanceof AquisitionRequest) {
            $aquisitionRequest = $model;

            $browser = new HttpBrowser(HttpClient::create([
                'verify_peer' => false,
                'verify_host' => false,
                'headers' => [
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/91.0.4472.124',
                    'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
                    'Accept-Language' => 'fr,fr-FR;q=0.8,en-US;q=0.5,en;q=0.3',
                ]
            ]));
            
            $crawler = $browser->request('GET', $aquisitionRequest->link_to_book);
            $content = $browser->getResponse()->getContent();

            if (empty($content)) {
                $message = 'Le contenu de la page n\'a pas été trouvé pour la demande d\'acquisition ' . $aquisitionRequest->title;
                Notification::make()
                    ->title('Contenu de la page non trouvé')
                    ->body($message)
                    ->danger()
                    ->send();
                Log::error($message);
                return;
            }

           


            $isbnCrawler = $crawler;
            $isbnNode = $isbnCrawler->filterXPath('//div[@id="ean"]');

            if($isbnNode->count() > 0) {
                if($isbnNode->attr('data-ean')) {

                    $aquisitionRequest->isbn = $isbnNode->attr('data-ean');
                }
                else {
                    $message = 'L\'ISBN n\'a pas été trouvé pour le livre ' . $aquisitionRequest->title;
                    Notification::make()
                        ->title('ISBN non trouvé');
                }
            }
            else {
                $message = 'L\'ISBN n\'a pas été trouvé pour le livre ' . $aquisitionRequest->title;
                Notification::make()
                    ->title('ISBN non trouvé')
                    ->body($message)
                    ->danger()
                    ->send();
                    Log::error($message);

            }

            $titleCrawler = $crawler;
            $titleNode = $titleCrawler->filterXPath('//div[@id="book-title-and-details"]//h1');
            if ($titleNode->count() > 0) {
                $aquisitionRequest->title = $titleNode->text();
            }
            else {
                $message = 'Le titre n\'a pas été trouvé pour le livre ' . $aquisitionRequest->title;
                Notification::make()
                    ->title('Titre non trouvé')
                    ->body($message)
                    ->danger()
                    ->send();
                Log::error($message);
            }
            $aquisitionRequest->link_to_book = 'parsed';
            $aquisitionRequest->save();

        }
    }
}