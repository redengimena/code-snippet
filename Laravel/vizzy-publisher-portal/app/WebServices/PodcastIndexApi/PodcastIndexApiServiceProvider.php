<?php

namespace App\WebServices\PodcastIndexApi;

use Illuminate\Support\ServiceProvider;
use App\WebServices\PodcastIndexApi\PodcastIndexApi;

class PodcastIndexApiServiceProvider extends ServiceProvider
{
    protected $defer = true;

    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        $this->app->bind(PodcastIndexApi::class, function ($app) {
            return new PodcastIndexApi(
                config('webservices.podcastindexapi.key'),
                config('webservices.podcastindexapi.secret'));
        });        
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [
            PodcastIndexApi::class,
        ];
    }
}
