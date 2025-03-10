<?php

namespace App\Services;

use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Models\Book;
use Illuminate\Support\Facades\Storage;
use DOMDocument;
use DOMXPath;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OpenLibraryService
{
    public function getBookPage(string $olKey): string
    {
        $pageContent = Http::get("https://openlibrary.org/works/{$olKey}");

        $dom = new DOMDocument();
        libxml_use_internal_errors(true);
        $dom->loadHTML($pageContent);
        libxml_clear_errors();

        return $dom->saveHTML();
    }


    public function extractBookDataFromOLKey(string $olKey): array
    {
        $pageContent = $this->getBookPage($olKey);

        $dom = new DOMDocument();
        libxml_use_internal_errors(true);
        $dom->loadHTML($pageContent);
        libxml_clear_errors();

        $xpath = new DOMXPath($dom);

        // Extract each value using specific XPath queries
        //1 class="work-title" itemprop="name">Peopleware</h1>
        $title          = $xpath->evaluate('string(//h1[@class="work-title"])');
        $subtileTitle   = $xpath->evaluate('string(//h2[@class="work-subtitle"])');
        $publishDate    = $xpath->evaluate('string(//span[@itemprop="datePublished"])');
        $publisher      = $xpath->evaluate('string(//*[@class="edition-omniline"]//span/a[@itemprop="publisher"])');
        $language       = $xpath->evaluate('string(//*[@class="edition-omniline"]//span[@itemprop="inLanguage"]/a)');
        $pages          = $xpath->evaluate('string(//*[@class="edition-omniline"]//span[@itemprop="numberOfPages"])');
        $isbn           = $xpath->evaluate('string(//dd[@itemprop="isbn"]/text())');
        $authorNodes    = $xpath->query('//a[@itemprop="author"]');
        $authors        = [];
        $coverUrl          = $xpath->evaluate('string(//img[@itemprop="image"]/@src)');
        
        if ($authorNodes) {
            foreach ($authorNodes as $node) {
                $authorName = trim($node->textContent);
                if (!in_array($authorName, $authors)) {
                    $authors[] = $authorName;
                }
            }
        }
        
        // Joindre les auteurs avec des virgules
        $author = implode(', ', array_unique($authors));

        $cleanTitle = trim(preg_replace('/\s+/', ' ', $title . ' ' . $subtileTitle));


        if($language == 'English') {
            $language = 'en';   
        } 
        elseif($language == 'French') {
            $language = 'fr';
        }   


    
        $data = [
            'title'                 => trim($cleanTitle),
            'year_of_publication'   => trim($publishDate),
            'publisher'             => trim($publisher),
            'lang'                  => trim($language),
            'pages'                 => trim($pages),
            'author'                => trim($author),
            'isbn'                  => trim($isbn),
            'cover_url'             => trim($coverUrl)
        ];

        return $data;
    }

    public function extractBookData($slug)
    {
        $bookInfoFile = Storage::disk('local')->get($slug.'.html');
        
        if(!$bookInfoFile) {
            throw new \Exception('Fichier HTML non trouvé');
        }

        $dom = new DOMDocument();
        $dom->loadHTML($bookInfoFile, LIBXML_NOERROR);
        $xpath = new DOMXPath($dom);

        // Extract each value using specific XPath queries
        $publishDate    = $xpath->evaluate('string(//*[@class="edition-omniline"]//span[@itemprop="datePublished"])');
        $publisher      = $xpath->evaluate('string(//*[@class="edition-omniline"]//span/a[@itemprop="publisher"])');
        $language       = $xpath->evaluate('string(//*[@class="edition-omniline"]//span[@itemprop="inLanguage"]/a)');
        $pages          = $xpath->evaluate('string(//*[@class="edition-omniline"]//span[@itemprop="numberOfPages"])');
        $isbn           = $xpath->evaluate('string(//dd[@itemprop="isbn"]/text())');
        $authorNodes    = $xpath->query('//a[@itemprop="author"]');
        $authors        = [];
        
        if ($authorNodes) {
            foreach ($authorNodes as $node) {
                $authorName = trim($node->textContent);
                if (!in_array($authorName, $authors)) {
                    $authors[] = $authorName;
                }
            }
        }
        
        // Joindre les auteurs avec des virgules
        $author = implode(', ', array_unique($authors));


        if($language == 'English') {
            $language = 'en';
        } 
        elseif($language == 'French') {
            $language = 'fr';
        }   
    
        $data = [
            'year_of_publication'   => trim($publishDate),
            'publisher'             => trim($publisher),
            'lang'                  => trim($language),
            
            'pages'                 => trim($pages),
            'author'                => trim($author),
            'isbn'                  => trim($isbn)
        ];

        return $data;
    }

}