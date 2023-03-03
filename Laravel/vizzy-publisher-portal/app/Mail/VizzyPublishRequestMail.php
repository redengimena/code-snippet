<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class VizzyPublishRequestMail extends Mailable
{
    use Queueable, SerializesModels;

    public $vizzy;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($vizzy)
    {
        $this->vizzy = $vizzy;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $environment = config('app.env');
        return $this->subject(strtoupper($environment) . ': Request Publishing of Vizzy')->markdown('emails.vizzy-publish-request');
    }
}
