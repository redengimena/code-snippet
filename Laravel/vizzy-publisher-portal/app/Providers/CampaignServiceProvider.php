<?php

namespace App\Providers;

use App\Campaign\CampaignDriverManager;
use App\Contracts\Campaign\CampaignManager;
use App\Listeners\Campaign\PodcasterSubscriber;
use Illuminate\Support\ServiceProvider;

class CampaignServiceProvider extends ServiceProvider
{
    /**
     * @var bool
     */
    protected $defer = false;

    /**
     * Register any other events for your application.
     *
     * @return void
     */
    public function boot()
    {
        $this->app['events']->subscribe(PodcasterSubscriber::class);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('campaign', function ($app) {
            return new CampaignDriverManager($app);
        });

        $this->app->bind(CampaignManager::class, function () {
            return $this->app['campaign']->manager();
        });

        $this->app->alias('campaign', CampaignDriverManager::class);
    }

    /**
     * Get the list of services provided.
     *
     * @return array
     */
    public function provides()
    {
        return ['campaign', CampaignDriverManager::class, CampaignManager::class];
    }
}