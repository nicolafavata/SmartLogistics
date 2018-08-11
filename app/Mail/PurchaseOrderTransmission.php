<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class PurchaseOrderTransmission extends Mailable
{
    use Queueable, SerializesModels;

    public $filename;
    public $name;
    public $provider;
    public $date;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($filename,$name,$provider,$date)
    {
        $this->filename = $filename;
        $this->name = $name;
        $this->provider = $provider;
        $this->date= $date;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('mails.PurchaseOrderTransmission')->attach($this->filename);
    }
}
