<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Illuminate\Contracts\Auth\Factory;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Session\Middleware\AuthenticateSession as BaseAuthenticateSession;

final class AuthenticateSessionMiddleware extends BaseAuthenticateSession
{
    /**
     * Get the guard instance that should be used by the middleware.
     */
    protected function guard(): Factory|Guard
    {
        return app(StatefulGuard::class);
    }
}
