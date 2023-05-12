<?php

namespace App\Console\Commands;

use App\Http\Controllers\Cron;
use Illuminate\Console\Command;

class cronSettingsDB2Excel extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cron:setting:db2Excel';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Esporta il database in Excel e lo invia alla mail indicata';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $cron = new Cron();
        $cron->db2Exel();
    }
}
