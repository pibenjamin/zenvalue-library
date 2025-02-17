<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Book;
use Carbon\Carbon;

class BookUpdateDateOfPublication extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:book-update-date-of-publication';

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
        $books = Book::whereNull('year_of_publication')->get();
        foreach ($books as $book) 
        {
            if($book->published_at !== null) 
            {
                $book->year_of_publication = Carbon::parse($book->published_at)->year;
                $book->save();
            }
        }
        $this->info('Date de publication mise à jour pour ' . $books->count() . ' livres.');
    }
}