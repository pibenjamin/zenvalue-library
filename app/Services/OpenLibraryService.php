<?php

namespace App\Services;

use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Models\Book;
use Illuminate\Support\Facades\Storage;
use DOMDocument;
use DOMXPath;



class OpenLibraryService
{

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
        $author         = $xpath->evaluate('string(//a[@itemprop="author"]/text())');


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