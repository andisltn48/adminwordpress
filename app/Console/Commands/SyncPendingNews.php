<?php

namespace App\Console\Commands;

use App\Models\BeritaWebsite;
use App\Models\Berita;
use App\Models\Website;
use App\Jobs\SyncNewsJob;
use Illuminate\Console\Command;

class SyncPendingNews extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'news:sync-pending';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Dispatch sync jobs for news that have not been successfully synced to WordPress';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $pending = BeritaWebsite::whereNull('wp_post_id')->get();

        if ($pending->isEmpty()) {
            $this->info('No pending syncs found.');
            return;
        }

        $this->info('Found ' . $pending->count() . ' pending syncs. Dispatching jobs...');

        foreach ($pending as $item) {
            $berita = Berita::find($item->berita_id);
            $website = Website::find($item->website_id);

            if ($berita && $website) {
                SyncNewsJob::dispatch($berita, $website, 1); // System user ID 1
                $this->line("Dispatched: Berita ID {$berita->id} to Website ID {$website->id}");
            }
        }

        $this->info('All pending syncs have been dispatched to the queue.');
    }
}
