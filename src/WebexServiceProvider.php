<?php

namespace Fabamb\LaravelWebex;

use Fabamb\LaravelWebex\Console\Commands\WebexSendCommand;
use GuzzleHttp\Client as HttpClient;
use Illuminate\Notifications\ChannelManager;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\ServiceProvider;

class WebexServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/../config/webex.php' => config_path('webex.php'),
        ], 'webex-config');

        if ($this->app->runningInConsole()) {
            $this->commands([
                WebexSendCommand::class,
            ]);
        }
    }

    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/webex.php', 'webex');

        $this->app->bind(WebexChannel::class, function (): WebexChannel {
            return new WebexChannel(
                new HttpClient,
                config('webex.url'),
                config('webex.token'),
            );
        });

        Notification::resolved(function (ChannelManager $service): void {
            $service->extend('webex', function ($app): WebexChannel {
                return $app->make(WebexChannel::class);
            });
        });
    }
}
