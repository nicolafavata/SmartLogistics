<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class SharingForecast extends Mailable
{
    use Queueable, SerializesModels;

    public $filename;
    public $name;
    public $dealer;
    public $provider;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($filename,$name,$dealer,$provider)
    {
        $this->filename = $filename;
        $this->name = $name;
        $this->dealer = $dealer;
        $this->provider = $provider;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('mails.SharingForecast')->attach($this->filename);
    }
}
