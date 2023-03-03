<?php

namespace App\Events;

use App\Models\User;

class PodcasterRegistered
{
    /**
     * @param \App\Models\User
     */
    public $podcaster;

    /**
     * Create a new event instance.
     *
     * @param \App\Models\User $User
     * @return void
     */
    public function __construct(User $podcaster)
    {
        $this->podcaster = $podcaster;
    }
}