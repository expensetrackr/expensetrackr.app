<?php

declare(strict_types=1);

namespace Workspaces;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Routing\Redirector;

trait RedirectsActions
{
    /**
     * Get the redirect response for the given action.
     */
    public function redirectPath(mixed $action): Application|RedirectResponse|Redirector|Response
    {
        if (method_exists($action, 'redirectTo')) {
            $response = $action->redirectTo();
        } else {
            $response = property_exists($action, 'redirectTo')
                ? $action->redirectTo
                : config('fortify.home');
        }

        return $response instanceof Response ? $response : redirect($response);
    }
}