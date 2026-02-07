<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\User;
use Illuminate\Http\Request;

class VerifyMail extends Mailable
{
    use Queueable, SerializesModels;
    public User $user;
    public string $verificationUrl;
     /**
     * Create a new message instance.
     */

    /**
     * Create a new message instance.
     */
    public function __construct(User $user, string $verificationUrl) 
    {
        $this->user = $user;
        $this->verificationUrl = $verificationUrl;

        
    }

   


    public function build(){

    return $this->subject('Verify your email address')
                ->markdown('emails.verify-email');
    }

    public function verifyEmail(Request $request, $id)
    {

        if(!$request->hasValidSignature()) {
            return response()->json(['message' => 'Invalid verification link.'], 401);
        }


        $user = User::findOrFail($id);
        if($user->email_verified_at) {
            return response()->json(['message' => 'Email already verified.'], 200);
        }
        $user->email_verified_at = now();
        $user->save();
        return response()->json(['message' => 'Email successfully verified.'], 200);







    }

}
