<?php

namespace App\Services;

use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\BrowserKit\HttpBrowser;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Log;
use App\Models\Training;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class TrainingImport
{
    protected string $trainingHomepage = 'https://zenvalue.catalogueformpro.com';
    protected HttpBrowser $browser;

    public function __construct()
    {
        $this->browser = new HttpBrowser(HttpClient::create([
            'verify_peer' => false,
            'verify_host' => false,
            'headers' => [
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/91.0.4472.124',
                'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
                'Accept-Language' => 'fr,fr-FR;q=0.8,en-US;q=0.5,en;q=0.3',
            ]
        ]));
    }

    public function import(): array
    {
        $crawler = $this->browser->request('GET', $this->trainingHomepage);
        $content = $this->browser->getResponse()->getContent();

        if (empty($content)) {
            $this->notifyError('Le contenu de la page n\'a pas été trouvé pour la page des formations');
            return ['success' => false, 'message' => 'Page content not found'];
        }

        $trainingNodes = $crawler->filterXPath('//div[@class="program"]');

        if ($trainingNodes->count() === 0) {
            return ['success' => false, 'message' => 'No trainings found'];
        }

        $stats = [
            'total' => $trainingNodes->count(),
            'imported' => 0,
            'skipped' => 0,
            'failed' => 0,
            'failures' => [],
        ];

        $trainingNodes->each(function ($node) use (&$stats) {
            $trainingData = $this->extractTrainingData($node);
            
            if (!$trainingData) {
                $stats['failed']++;
                $stats['failures'][] = [
                    'title' => $node->filter('h2')->count() > 0 
                        ? $node->filter('h2')->first()->text() 
                        : 'Formation sans titre',
                    'reason' => $this->getExtractionError($node)
                ];
                return;
            }

            if ($this->trainingExists($trainingData)) {
                $stats['skipped']++;
                return;
            }

            if ($this->createTraining($trainingData)) {
                $stats['imported']++;
            } else {
                $stats['failed']++;
                $stats['failures'][] = [
                    'title' => $trainingData['title'],
                    'reason' => "Échec lors de la création de la formation (problème avec l'image ou la base de données)"
                ];
            }
        });

        return ['success' => true, 'stats' => $stats];
    }

    protected function extractTrainingData($node): ?array
    {
        $trainingLink = $node->filter('a')->count() > 0 
            ? $node->filter('a')->first()->attr('href') 
            : null;

        if (!$trainingLink) {
            return null;
        }

        $trainingUrl = $this->trainingHomepage . $trainingLink;
        $trainingImage = $node->filter('img')->count() > 0 
            ? $node->filter('img')->first()->attr('src') 
            : null;
        $trainingTitle = $node->filter('h2')->count() > 0 
            ? $node->filter('h2')->first()->text() 
            : null;

        if (!$trainingTitle || !$trainingImage) {
            return null;
        }

        return [
            'title' => $trainingTitle,
            'url' => $trainingUrl,
            'image_url' => $trainingImage,
        ];
    }

    protected function trainingExists(array $data): bool
    {
        return Training::where('title', $data['title'])
            ->where('url', $data['url'])
            ->exists();
    }

    protected function createTraining(array $data): bool
    {
        $filename = $this->downloadAndStoreImage($data['image_url']);
        
        if (!$filename) {
            return false;
        }

        return Training::create([
            'title' => $data['title'],
            'url' => $data['url'],
            'image' => $filename,
        ]) !== null;
    }

    protected function downloadAndStoreImage(string $imageUrl): ?string
    {
        $response = Http::withOptions(['verify' => false])->get($imageUrl);

        if (!$response->successful()) {
            $this->notifyError('Failed to download image: ' . $imageUrl);
            return null;
        }

        $filename = 'trainings/' . Str::uuid() . '.jpg';
        
        if (!Storage::disk('public')->put($filename, $response->body())) {
            $this->notifyError('Failed to save image to storage');
            return null;
        }

        return $filename;
    }

    protected function notifyError(string $message): void
    {
        Notification::make()
            ->title('Erreur d\'importation')
            ->body($message)
            ->danger()
            ->send();
        Log::error($message);
    }

    protected function getExtractionError($node): string
    {
        if ($node->filter('a')->count() === 0) {
            return "Lien de la formation manquant";
        }
        if ($node->filter('img')->count() === 0) {
            return "Image de la formation manquante";
        }
        if ($node->filter('h2')->count() === 0) {
            return "Titre de la formation manquant";
        }
        return "Structure de données invalide";
    }
} 