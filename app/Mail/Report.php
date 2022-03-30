<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class Report extends Mailable
{
    use Queueable, SerializesModels;

    protected $attach_path, $host;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($args = array())
    {
        $this->attach_path = $args['attach_path'];
        $this->host = $args['host'];
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $files = Storage::disk('public')->files($this->attach_path);

        $mail = $this->from('emporioapp@emporiosd.it', 'EmporioApp')
            ->subject('[EmporioApp ' . strtoupper($this->host) . '] - Report prodotti FEAD');

        foreach ($files as $file) {

            if (substr($file, -3, 3) == 'csv') {

                $mail->attachFromStorageDisk(
                    'public',
                    $file
                );
            }
        }

        return $mail->view('mail.report');

    }
}
