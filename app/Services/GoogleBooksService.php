<?php

namespace App\Services;

use App\Models\Book;
use App\Models\Author;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Filament\Notifications\Notification;
use Illuminate\Support\Str;

class GoogleBooksService
{
    private $apiKey;
    private $baseUrl = 'https://www.googleapis.com/books/v1/volumes';

    public function __construct()
    {
        $this->apiKey = config('services.google.api_key');
        if (empty($this->apiKey)) {
            throw new \Exception('Google Books API key is not configured. Please add GOOGLE_CLOUD_API to your .env file.');
        }
    }

    /**
     * Importe les informations d'un livre depuis Google Books
     *
     * @param Book $book
     * @return bool
     */
    public function importBookData(Book $book): bool
    {
        try {
            if (empty($book->isbn)) {
                $this->logError('ISBN manquant pour le livre', ['book_id' => $book->id]);
                return false;
            }

            $response = Http::get($this->baseUrl, [
                'q' => "isbn:{$book->isbn}",
                'key' => $this->apiKey,
            ]);

            if (!$response->successful()) {
                $this->logError('Erreur API Google Books', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                    'isbn' => $book->isbn
                ]);
                return false;
            }

            $data = $response->json();

            if (empty($data['items'])) {
                Log::info("Aucun livre trouvé pour l'ISBN : {$book->isbn}");
                return false;
            }

            $bookData = $data['items'][0]['volumeInfo'];
            
            // Mise à jour des informations du livre
            $book->title = $bookData['title'] ?? $book->title;
            $book->pages = $bookData['pageCount'] ?? $book->pages;
            $book->year_of_publication = $this->extractYear($bookData['publishedDate'] ?? null);
            $book->dimensions = $this->formatDimensions($bookData['dimensions'] ?? []);
            $book->publisher = $bookData['publisher'] ?? $book->publisher;
            $book->lang = $this->mapLanguage($bookData['language'] ?? null);
            $book->slug = Str::slug($book->title);
            $book->status = Book::STATUS_TO_QUALIFY;
            $book->parsed = true;

            // Gestion des auteurs
            if (!empty($bookData['authors'])) {
                foreach ($bookData['authors'] as $authorName) {
                    $author = Author::firstOrCreate(['name' => $authorName]);
                    if (!$book->authors()->where('authors.id', $author->id)->exists()) {
                        $book->authors()->attach($author);
                    }
                }
            }

            // Gestion de la couverture
            if (!empty($bookData['imageLinks']['thumbnail'])) {
                $book->cover_url = $this->downloadAndSaveCover($bookData['imageLinks']['thumbnail'], $book);
            }

            $book->save();
            return true;

        } catch (\Exception $e) {
            $this->logError('Exception lors de l\'import Google Books', [
                'message' => $e->getMessage(),
                'book_id' => $book->id
            ]);
            return false;
        }
    }

    /**
     * Extrait l'année de publication
     *
     * @param string|null $date
     * @return string|null
     */
    private function extractYear(?string $date): ?string
    {
        if (!$date) return null;
        if (preg_match('/\d{4}/', $date, $matches)) {
            return $matches[0];
        }
        return null;
    }

    /**
     * Formate les dimensions
     *
     * @param array $dimensions
     * @return string|null
     */
    private function formatDimensions(array $dimensions): ?string
    {
        if (empty($dimensions)) {
            return null;
        }

        $parts = [];
        if (isset($dimensions['height'])) {
            $parts[] = "H: {$dimensions['height']}";
        }
        if (isset($dimensions['width'])) {
            $parts[] = "L: {$dimensions['width']}";
        }
        if (isset($dimensions['thickness'])) {
            $parts[] = "E: {$dimensions['thickness']}";
        }

        return !empty($parts) ? implode(' x ', $parts) : null;
    }

    /**
     * Convertit le code de langue
     *
     * @param string|null $language
     * @return string|null
     */
    private function mapLanguage(?string $language): ?string
    {
        if (!$language) return null;
        
        return match (strtolower($language)) {
            'fr', 'french' => 'fr',
            'en', 'english' => 'en',
            default => null,
        };
    }

    /**
     * Télécharge et sauvegarde la couverture
     *
     * @param string $url
     * @param Book $book
     * @return string|null
     */
    private function downloadAndSaveCover(string $url, Book $book): ?string
    {
        try {
            $response = Http::get($url);
            if (!$response->successful()) {
                return null;
            }

            $filename = 'books/covers/' . (string) Str::uuid() . '.jpg';
            $saved = Storage::disk('public')->put($filename, $response->body());

            return $saved ? $filename : null;
        } catch (\Exception $e) {
            Log::error('Erreur lors du téléchargement de la couverture', [
                'url' => $url,
                'book_id' => $book->id,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Log une erreur avec notification
     *
     * @param string $message
     * @param array $context
     * @return void
     */
    private function logError(string $message, array $context = []): void
    {
        Log::error($message, $context);
        
        Notification::make()
            ->title('Erreur Google Books')
            ->body($message)
            ->danger()
            ->send();
    }

    public function searchByIsbn(string $isbn): ?array
    {
        try {
            $response = Http::get($this->baseUrl, [
                'q' => "isbn:{$isbn}",
                'key' => $this->apiKey
            ]);

            if (!$response->successful()) {
                Log::error('Google Books API error', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                    'isbn' => $isbn
                ]);
                throw new \Exception("Google Books API error: " . $response->body());
            }

            $data = $response->json();

            if (empty($data['items'])) {
                Log::info('No book found for ISBN', ['isbn' => $isbn]);
                return null;
            }

            $book = $data['items'][0]['volumeInfo'];
            $saleInfo = $data['items'][0]['saleInfo'] ?? [];

            return [
                'title' => $book['title'] ?? null,
                'authors' => $book['authors'] ?? [],
                'publisher' => $book['publisher'] ?? null,
                'published_date' => $book['publishedDate'] ?? null,
                'description' => $book['description'] ?? null,
                'page_count' => $book['pageCount'] ?? null,
                'categories' => $book['categories'] ?? [],
                'language' => $book['language'] ?? null,
                'cover_url' => $book['imageLinks']['thumbnail'] ?? null,
                'price' => $saleInfo['listPrice']['amount'] ?? null,
                'currency' => $saleInfo['listPrice']['currencyCode'] ?? null,
                'isbn' => $isbn,
                'dimensions' => $this->extractDimensions($book['dimensions'] ?? null),
            ];
        } catch (\Exception $e) {
            Log::error('Error fetching book data from Google Books', [
                'isbn' => $isbn,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    private function extractDimensions(?array $dimensions): ?string
    {
        if (!$dimensions) {
            return null;
        }

        $parts = [];
        if (isset($dimensions['height'])) {
            $parts[] = "H: {$dimensions['height']}";
        }
        if (isset($dimensions['width'])) {
            $parts[] = "L: {$dimensions['width']}";
        }
        if (isset($dimensions['thickness'])) {
            $parts[] = "E: {$dimensions['thickness']}";
        }

        return !empty($parts) ? implode(' x ', $parts) : null;
    }
} 