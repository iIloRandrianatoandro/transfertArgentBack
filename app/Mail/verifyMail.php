<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Address;

class verifyMail extends Mailable
{
    use Queueable, SerializesModels;


    public function __construct() 
    {
        //
    }

    public function envelope() //can add from and reply address
    {
        return new Envelope(
            subject: 'Verification adresse Mail',
        );
    }

    public function content()
    {
        return new Content(
            view : 'verifyMail',
        );
    }
    
    public function attachments() //pdf, image, ...
    {
        return [];
    }
}
