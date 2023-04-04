<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EnvioNormalMail extends Mailable
{
    use Queueable, SerializesModels;

    public $subject;
    public $mensaje;
    public $sender_mail;
    public $sender_name;
    public $pdf; 

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($subject, $mensaje, $sender_mail, $sender_name)
    {
        $this->subject = $subject;
        $this->mensaje = $mensaje;
        $this->sender_mail = $sender_mail;
        $this->sender_name = $sender_name;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from($this->sender_mail, $this->sender_name)
        ->subject($this->subject)
        ->html($this->mensaje);
        
    }
}
