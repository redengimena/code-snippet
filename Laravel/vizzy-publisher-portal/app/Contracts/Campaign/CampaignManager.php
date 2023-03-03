<?php

namespace App\Contracts\Campaign;

use App\Models\User;

interface CampaignManager
{
    /**
     * Add the podcaster to default podcaster contact list.
     *
     * @param \App\Models\User $podcaster
     * @return mixed
     */
    public function addPodcasterContact(User $podcaster);

}
