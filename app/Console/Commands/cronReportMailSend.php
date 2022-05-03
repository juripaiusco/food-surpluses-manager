<?php

namespace App\Console\Commands;

use App\Http\Controllers\Cron;
use Illuminate\Console\Command;

class cronReportMailSend extends Command
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
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $cron = new Cron();
        $cron->reportSend();
    }
}
