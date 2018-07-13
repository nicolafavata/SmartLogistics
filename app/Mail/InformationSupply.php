<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class InformationSupply extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $messaggio;
    public $rag_soc;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($user,$messaggio,$rag_soc)
    {
        $this->user = $user;
        $this->messaggio = $messaggio;
        $this->rag_soc = $rag_soc;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('mails.informationsupply');
    }
}
