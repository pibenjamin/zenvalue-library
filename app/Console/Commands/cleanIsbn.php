<?php

namespace App\Console\Commands;

use App\Models\Book;

use Illuminate\Console\Command;

class cleanIsbn extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:clean-isbn';

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
        $this->info('Début de la nettoyage des isbn');
        $books = Book::all();
        foreach ($books as $book) {
            $this->info('ISBN pour le livre "' . $book->isbn . '"');
            $book->isbn = trim(str_replace('-', '', $book->isbn));
            $book->save();
            $this->info('ISBN pour le livre "' . $book->isbn . '"');
            $this->info('Nettoyage de l\'isbn terminé pour le livre ' . $book->title . ' (' . $book->id . ')');
        }
        $this->info('Nettoyage des isbn terminé');
    }
}
