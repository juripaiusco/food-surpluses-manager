<?php

namespace App\Console\Commands;

use App\Http\Controllers\Report;
use Illuminate\Console\Command;

class reportSendMail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mail:report';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Invio dei report come allegato CSV';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $report = new Report();
        $report->mailSend();
    }
}
