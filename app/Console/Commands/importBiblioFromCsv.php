<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Book;
use App\Models\Author;
use App\Models\Training;
use Illuminate\Support\Facades\Storage;
use League\Csv\Reader;

class ImportBiblioFromCsv extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:biblio {training_id : The ID of the training to link books to}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import bibliographic data from CSV file and link to training';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $trainingId = $this->argument('training_id');
        $training = Training::find($trainingId);

        if (!$training) {
            $this->error("Training with ID {$trainingId} not found.");
            return 1;
        }

        $this->info("Importing bibliographic data from CSV file for training ID {$trainingId}");

        $csvPath = storage_path('app/public/biblios/biblio-'.$trainingId.'.csv');

        if (!file_exists($csvPath)) {
            $this->error("CSV file not found at {$csvPath}");
            return 1;
        }

        $csv = Reader::createFromPath($csvPath, 'r');
        $csv->setHeaderOffset(0);

        $records = $csv->getRecords();
        $count = 0;

        foreach ($records as $record) {
            // Create or update book
            $book = Book::firstOrCreate(
                ['title' => $record['Titre']],
                [
                    'title' => $record['Titre'],
                    'language' => $record['Langue'],
                    'owner_id' => 1,
                    'support_id' => 1,
                    'status' => Book::STATUS_TO_QUALIFY,
                ]
            );

            // Process authors
            if (!empty($record['Auteurs'])) {
                $authors = explode('&', $record['Auteurs']);
                foreach ($authors as $authorName) {
                    $authorName = trim($authorName);
                    $author = Author::firstOrCreate(
                        ['name' => $authorName],
                        ['name' => $authorName]
                    );

                    // Attach author to book if not already attached
                    if (!$book->authors()->where('author_id', $author->id)->exists()) {
                        $book->authors()->attach($author->id);
                    }
                }
            }

            // Attach book to training if not already attached
            if (!$training->books()->where('book_id', $book->id)->exists()) {
                $training->books()->attach($book->id);
            }

            $count++;
        }

        $this->info("Successfully imported {$count} books and linked them to training ID {$trainingId}");
        return 0;
    }
}
