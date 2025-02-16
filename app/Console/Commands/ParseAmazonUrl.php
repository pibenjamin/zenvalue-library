<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DOMXPath;
use DOMDocument;

class ParseAmazonUrl extends Command
{
    /**
     * The name and signature of the console command.
     *
     * 
     * 
     * 
     * 
     * @var string
     */
    protected $signature = 'app:parse-amazon-url';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */


    public function handle()
    {
        // Sinon, parcourir les livres de la BDD
        $books = \App\Models\Book::whereNull('isbn')
            ->whereNotNull('amazon_content_page')
            ->get();

        $this->info("Found {$books->count()} books to process");

        foreach ($books as $book) {
            $this->info("Processing book ID: {$book->id}");
            
            $foundDatas = $this->parseContent($book->amazon_content_page);
            
            if (!empty($foundDatas)) {
                $book->update($foundDatas);
                $this->info("Updated book {$book->id} with: " . json_encode($foundDatas));
            }
        }
    }

    private function parseContent($content)
    {
        $dom = new DOMDocument();
        libxml_use_internal_errors(true);
        $dom->loadHTML($content);
        libxml_clear_errors();
        
        $xpath = new DOMXPath($dom);
        $foundDatas = [];



        $patterns = [
            'pages' => [
                'xpath' => [
                    "//*[@id='rpi-attribute-book_details-fiona_pages']/div[3]/span",
                ],
                'regex' => '/(\d+)/i',
                'validate' => function($value) {
                    return (int) preg_replace('/[^0-9]/', '', $value);
                }
            ],
            'editor' => [
                'xpath' => [
                    "//*[@id='rpi-attribute-book_details-publisher']/div[3]/span"
                ],
                'regex' => '/(Éditeur|Publisher)\s*:\s*([^;(]+)/i',
                'validate' => function($value) {
                    $value = trim($value);
                    return $value !== 'Éditeur' && $value !== 'Publisher' ? $value : null;
                }
            ],
            'publication_date' => [
                'xpath' => [
                    "//*[@id='rpi-attribute-book_details-publication_date']/div[3]/span"
                ],
                'regex' => '/(Date de publication|Publication date)\s*:\s*([^;(]+)/i'
            ],
            'language' => [
                'xpath' => [
                    "//*[@id='rpi-attribute-language']/div[3]/span"                    
                ],
                'regex' => '/(Langue|Language)\s*:\s*([^;(]+)/i'
            ],
            'isbn13' => [
                'xpath' => [
                    "//*[@id='rpi-attribute-book_details-isbn13']/div[3]/span"
                ],
                'regex' => '/ISBN-13\s*:\s*(\d{13})/i'
            ]
        ];

        foreach ($patterns as $key => $pattern) {
            foreach ($pattern['xpath'] as $xpath_pattern) {
                $nodes = $xpath->query($xpath_pattern);

                dump($nodes->length);
                
                if ($nodes->length > 0) {
                    foreach ($nodes as $node) {
                        $text = $node->textContent;
                        $this->info("Recherche de $key : " . $text);
                        
                        if (preg_match($pattern['regex'], $text, $matches)) {
                            $value = isset($matches[2]) ? $matches[2] : $matches[1];
                            if (isset($pattern['validate'])) {
                                $value = $pattern['validate']($value);
                                if ($value === null) continue;
                            }
                            $foundDatas[$key] = $value;
                            $this->info("$key trouvé : " . $foundDatas[$key]);
                            break 2;
                        }
                    }
                }
            }
        }

        // Afficher le tableau des résultats
        $this->table(
            ['Attribut', 'Valeur'],
            collect($foundDatas)
                ->map(fn($value, $key) => [$key, is_string($value) ? trim($value) : $value])
                ->toArray()
        );

        return $foundDatas;
    }
}
