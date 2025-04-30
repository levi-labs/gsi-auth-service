<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SendEmail extends Mailable
{
    use Queueable, SerializesModels;

    protected User $user;
    protected string $verificationLink;
    protected string $type;

    /**
     * Create a new message instance.
     */
    public function __construct(User $user, string $verificationLink, string $type = 'verification')
    {
        $this->user = $user;
        $this->verificationLink = $verificationLink;
        $this->type = $type;
    }



    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {

        return new Envelope(
            from: 'GSI-verify@example.com',
            subject: $this->type == 'verification' ? 'Verify your email address' : 'Reset your password',

        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        // if ($this->type == 'verification') {
        //     $this->view('email.verification-email');
        // } else {
        //     $this->view('email.password-reset-email');
        // }
        return new Content(
            view: 'email.verification-email',
            with: [
                'verificationLink' => $this->verificationLink,
                'user' => $this->user,
                'type' => $this->type,
            ],
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
