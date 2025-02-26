<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Tag;
use Illuminate\Support\Str;

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
    protected $description = 'Nettoyage des tags';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Récupérer tous les tags et transforme les en mots en minuscule et joute des ->info()
        $tags = Tag::all();
        foreach ($tags as $tag) {

            $this->info($tag->title);
            $tag->title = strtolower($tag->title);
            $tag->slug = Str::slug($tag->title);
            $tag->save();
            $this->info('Tag nettoyé');
        }
        $this->info('Nettoyage des tags terminé ('.count($tags).' tags nettoyés)');
    }
}
