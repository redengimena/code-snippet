<?php

namespace App\Events;

use App\Models\Vizzy;

class VizzyCreated
{
    /**
     * @param \App\Models\Vizzy
     */
    public $vizzy;

    /**
     * Create a new event instance.
     *
     * @param \App\Models\Vizzy $vizzy
     * @return void
     */
    public function __construct(Vizzy $vizzy)
    {
        $this->vizzy = $vizzy;
    }
}