<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class MonitoringExpire extends Mailable
{
    use Queueable, SerializesModels;

    public $filename;
    public $name;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($filename,$name)
    {
        $this->filename = $filename;
        $this->name = $name;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('mails.MonitoringExpire')->attach($this->filename);
    }
}
