<?php

namespace App\Mail;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PasswordResetMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */

    public User $user;
    public string $resetUrl;
    public string $token;
    public function __construct($user, $resetUrl, $token)
    {
        //
        $this->token = $token;
        $this->resetUrl = $resetUrl;
        $this->user = $user;
    }

    public function build()
    {
        return $this->subject('Password Reset Request')
                    ->markdown('emails.password-reset');
    }


    
}
