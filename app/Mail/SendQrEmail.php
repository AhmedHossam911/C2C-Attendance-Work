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
    public $qrCode; // Renamed from qrImage to qrCode for clarity (it's an SVG string now)

    public function __construct($data, $qrCode)
    {
        $this->data = $data;
        $this->qrCode = $qrCode;
    }

    public function envelope(): Envelope
    {
        $subject = 'Membership QR – ' . $this->data['committee_name'];
        if (!empty($this->data['session_name'])) {
            $subject .= ' – ' . $this->data['session_name'];
        }

        return new Envelope(
            subject: $subject,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.qr_code',
            with: [
                'data' => $this->data,
                'qrCode' => $this->qrCode,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
