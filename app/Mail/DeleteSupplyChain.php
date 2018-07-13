<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class DeleteSupplyChain extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $rag;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($user,$rag)
    {
        $this->user = $user;
        $this->rag = $rag;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('mails.deletesupply');
    }
}
