<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class BatchSalesList extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $store;
    public $problem;
    public $date;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($user,$store,$problem,$date)
    {
        $this->user = $user;
        $this->store = $store;
        $this->problem = $problem;
        $this->date = $date;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('mails.batchsaleslist');
    }
}
