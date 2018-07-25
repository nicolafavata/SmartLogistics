<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class BatchMappingProvider extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $store;
    public $date;
    public $up;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($user,$store,$date,$up)
    {
        $this->user = $user;
        $this->store = $store;
        $this->date = $date;
        $this->up = $up;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('mails.batchmappingprovider');
    }
}
