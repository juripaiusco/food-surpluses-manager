<?php

namespace App\Console\Commands;

use App\Http\Controllers\Customer;
use Illuminate\Console\Command;

class cronCustomerActiveReset extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cron:customer:active-reset';

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
        $customers = new Customer();
        $customers->active_reset();
    }
}
