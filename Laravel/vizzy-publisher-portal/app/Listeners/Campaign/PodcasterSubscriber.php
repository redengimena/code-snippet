<?php

namespace App\Listeners\Campaign;

use App\Events\PodcasterRegistered;
use App\Jobs\Campaign\SendPodcasterRegistered;
use App\Contracts\Campaign\CampaignManager;
use Illuminate\Contracts\Queue\ShouldQueue;

class PodcasterSubscriber implements ShouldQueue
{
    /**
     * Handle registered podcaster event.
     *
     * @param \App\Events\PodcasterRegistered $event
     * @param void
     */
    public function registered(PodcasterRegistered $event)
    {
        SendPodcasterRegistered::dispatch($event->podcaster);
    }

    /**
     * Register the listeners for the subscriber.
     *
     * @param \Illuminate\Events\Dispatcher $events
     * @return void
     */
    public function subscribe($events)
    {
        $events->listen(
            PodcasterRegistered::class,
            [PodcasterSubscriber::class, 'registered']
        );
    }

    /**
     * Get the campaign manager instance.
     *
     * @return \App\Contracts\Campaign\CampaignManager
     */
    protected function manager()
    {
        return app(CampaignManager::class);
    }
}