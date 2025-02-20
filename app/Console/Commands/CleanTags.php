<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Tag;
class CleanTags extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:clean-tags';

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
        // Récupérer tous les tags et transforme les en mots en minuscule
        $tags = Tag::all();
        foreach ($tags as $tag) {
            $tag->name = strtolower($tag->name);
            $tag->slug = strtolower($tag->name);
            $tag->save();
        }
    }
}
