<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class Report extends Mailable
{
    use Queueable, SerializesModels;

    protected $attach_path, $host;

    /**
     * Create a new message instance.
     */
    public function __construct($args = array())
    {
        $this->attach_path = $args['attach_path'];
        $this->host = $args['host'];
        $this->date_send = $args['date_send'];
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address('emporioapp@emporiosd.it', 'EmporioApp'),
            subject: '[EmporioApp ' . strtoupper($this->host) . '] - Report prodotti FEAD del ' . $this->date_send
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'mail.report',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        $array_attachments = array();
        $files = Storage::disk('public')->files($this->attach_path);

        foreach ($files as $file) {

            if (substr($file, -3, 3) == 'csv') {

                $array_attachments[] = Attachment::fromStorageDisk('public', $file);
            }
        }

        return $array_attachments;
    }
}
