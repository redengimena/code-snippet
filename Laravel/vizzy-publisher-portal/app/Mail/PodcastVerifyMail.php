<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PodcastVerifyMail extends Mailable
{
    use Queueable, SerializesModels;

    public $podcastVerification;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($podcastVerification)
    {
        $this->podcastVerification = $podcastVerification;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Please verify ownership of Podcast')->markdown('emails.podcast-verify');
    }
}
