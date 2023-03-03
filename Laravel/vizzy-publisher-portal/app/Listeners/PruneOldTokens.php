<?php

namespace App\Listeners;

use Laravel\Passport\Token;
use Laravel\Passport\Events\AccessTokenCreated;


class PruneOldTokens {

    /**
     * Handle the event.
     *
     * @param  AccessTokenCreated  $event
     * @return void
     */
    public function handle(AccessTokenCreated $event)
    {
        \DB::table('oauth_access_tokens')
        ->whereDate('expires_at', '<', now()->addDays(-1))
        ->delete();
    }

  }