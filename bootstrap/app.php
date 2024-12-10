<?php

declare(strict_types=1);

use App\Http\Middleware\AddWorkspaceToRequest;
use App\Http\Middleware\HandleInertiaRequests;
use App\Http\Middleware\SetLanguage;
use App\Http\Middleware\ShareInertiaDataMiddleware;
use App\Http\Middleware\ValidateAccountWizard;
use App\Http\Middleware\WorkspacesPermission;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(append: [
            ShareInertiaDataMiddleware::class,
            HandleInertiaRequests::class,
            AddLinkHeadersForPreloadedAssets::class,
            AddWorkspaceToRequest::class,
            WorkspacesPermission::class,
            SetLanguage::class,
        ]);

        $middleware->alias([
            'accounts.create.wizard' => ValidateAccountWizard::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
