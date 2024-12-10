<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Laravel\Fortify\Events\PasswordUpdatedViaController;

final class EventServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Event::listen(function (PasswordUpdatedViaController $event): void {
            if (request()->hasSession()) {
                request()->session()->put(['password_hash_sanctum' => Auth::user()?->getAuthPassword()]);
            }
        });
    }
}
