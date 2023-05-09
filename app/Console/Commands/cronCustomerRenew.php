<?php

namespace App\Console\Commands;

use App\Http\Controllers\Cron;
use Illuminate\Console\Command;

class cronCustomerRenew extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cron:customer:renew';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Rinnovo dei punti clienti';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $cron = new Cron();
        $cron->customerRenew();
    }
}
