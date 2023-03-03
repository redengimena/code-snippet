<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AdminPodcastTransferMail extends Mailable
{
    use Queueable, SerializesModels;

    public $podcast;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($podcast)
    {
        $this->podcast = $podcast;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $environment = config('app.env');
        return $this->subject(strtoupper($environment) . ': Admin Podcast Transferred')->markdown('emails.admin-podcast-transfer');
    }
}
