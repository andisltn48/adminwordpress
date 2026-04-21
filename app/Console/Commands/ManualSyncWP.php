<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\BeritaController;

class ManualSyncWP extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:wordpress-manual';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Menjalankan sinkronisasi manual berita ke semua website WordPress';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Memulai sinkronisasi manual...');
        
        $controller = new BeritaController();
        $controller->manualSyncToWP();

        $this->info('Sinkronisasi manual selesai.');
    }
}
