<?php

declare(strict_types=1);

namespace App\Providers;

use App\Services\CurrencyService;
use GuzzleHttp\Client;
use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;

final class CurrencyServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(CurrencyService::class, function (Application $app): CurrencyService {
            $apiKey = type(config('services.currency_api.key'))->asString();
            $baseUrl = type(config('services.currency_api.base_url'))->asString();
            $client = $app->make(Client::class);

            return new CurrencyService($apiKey, $baseUrl, $client);
        });
    }

    public function boot(): void
    {
        //
    }
}