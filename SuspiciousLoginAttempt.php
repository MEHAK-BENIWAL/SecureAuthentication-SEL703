<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SuspiciousLoginAttempt extends Mailable
{
    use Queueable, SerializesModels;
    public $email, $ip, $attempts;

    /**
     * Create a new message instance.
     */
    public function __construct($email, $ip, $attempts)
    {
        $this->email = $email;
        $this->ip = $ip;
        $this->attempts = $attempts;
    }
    public function build()
    {
        return $this->subject('⚠️ Suspicious Login Detected')
                    ->markdown('mails.suspicious-login');
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Suspicious Login Attempt',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'view.name',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
