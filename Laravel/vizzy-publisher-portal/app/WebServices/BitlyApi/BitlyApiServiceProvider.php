<?php

namespace App\WebServices\BitlyApi;

use Illuminate\Support\ServiceProvider;
use App\WebServices\BitlyApi\BitlyApi;

class BitlyApiServiceProvider extends ServiceProvider
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
        $this->app->bind(BitlyApi::class, function ($app) {
            return new BitlyApi(
                config('webservices.bitlyapi.access_token',''),
                config('webservices.bitlyapi.custom_domain',''),
                config('webservices.bitlyapi.group_guid',''));
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
            BitlyApi::class,
        ];
    }
}
