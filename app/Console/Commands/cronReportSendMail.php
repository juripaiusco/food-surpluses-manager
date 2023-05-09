<?php

namespace App\Console\Commands;

use App\Http\Controllers\Cron;
use Illuminate\Console\Command;

class cronReportSendMail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cron:report:mailsend';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Invio report via mail';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $cron = new Cron();
        $cron->reportSend();
    }
}
