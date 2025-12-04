<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Attachment;

class SendQrEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $data;
    public $qrImage;

    public function __construct($data, $qrImage)
    {
        $this->data = $data;
        $this->qrImage = $qrImage;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Membership QR – ' . $this->data['committee_name'] . ' – ' . $this->data['session_name'],
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.qr_code',
        );
    }

    public function attachments(): array
    {
        return [
            Attachment::fromData(fn() => $this->qrImage, 'qr_code.png')
                ->withMime('image/png'),
        ];
    }
}
