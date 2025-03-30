<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\BrowserKit\HttpBrowser;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Log;
use App\Models\Training;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Services\TrainingImport;

class importTrainings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:import-trainings';

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
        $importer = new TrainingImport();
        $result = $importer->import();

        if (!$result['success']) {
            $this->error($result['message']);
            return 1;
        }

        $stats = $result['stats'];
        $this->info("Import completed:");
        $this->info("Total trainings found: {$stats['total']}");
        $this->info("Successfully imported: {$stats['imported']}");
        $this->info("Skipped (already exists): {$stats['skipped']}");
        $this->info("Failed: {$stats['failed']}");

        if (!empty($stats['failures'])) {
            $this->error("\nFailed imports:");
            foreach ($stats['failures'] as $failure) {
                $this->error("- {$failure['title']}: {$failure['reason']}");
            }
        }

        return 0;
    }
}
