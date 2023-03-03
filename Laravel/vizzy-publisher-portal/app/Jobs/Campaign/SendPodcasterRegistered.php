<?php

namespace App\Jobs\Campaign;

use App\Models\User;
use App\Campaign\CampaignDriverManager;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;

class SendPodcasterRegistered implements ShouldQueue
{
    use InteractsWithQueue, Dispatchable, Queueable;

    /**
     * @var \App\Models\User
     */
    protected $podcaster;

    /**
     * Create new instance of the job.
     *
     * @param \App\Models\User $podcaster
     * @param string $activationUrl
     */
    public function __construct(User $podcaster)
    {
        $this->podcaster = $podcaster;
    }

    /**
     * Execute the job.
     *
     * @param \App\Events\PodcasterRegistered $event
     * @param mixed
     */
    public function handle(CampaignDriverManager $driverManager)
    {
        $driverManager->manager('activecampaign')->addPodcasterContact($this->podcaster);
    }
}