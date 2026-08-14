<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ContactMessageMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $senderName,
        public string $senderEmail,
        public string $subjectLine,
        public string $body,
    ) {
    }

    public function build(): self
    {
        return $this->replyTo($this->senderEmail, $this->senderName)
            ->subject('Contact Form: ' . $this->subjectLine)
            ->view('emails.contact-message');
    }
}
