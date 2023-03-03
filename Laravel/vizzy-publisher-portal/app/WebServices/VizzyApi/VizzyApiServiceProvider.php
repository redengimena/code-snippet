<?php

namespace App\WebServices\VizzyApi;

use Illuminate\Support\ServiceProvider;
use App\WebServices\VizzyApi\VizzyApi;

class VizzyApiServiceProvider extends ServiceProvider
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
        $this->app->bind(VizzyApi::class, function ($app) {
            return new VizzyApi(
                config('webservices.vizzyapi.base_url'),
                config('webservices.vizzyapi.client_id'),
                config('webservices.vizzyapi.client_secret'));
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
            VizzyApi::class,
        ];
    }
}
