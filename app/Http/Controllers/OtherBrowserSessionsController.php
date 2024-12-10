<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\User;
use Cjmellor\BrowserSessions\Facades\BrowserSessions;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Actions\ConfirmPassword;

final class OtherBrowserSessionsController extends Controller
{
    /**
     * Log out from other browser sessions.
     *
     * @throws AuthenticationException
     */
    public function destroy(Request $request, StatefulGuard $guard): RedirectResponse
    {
        $confirmed = app(ConfirmPassword::class)(
            $guard,
            type($request->user())->as(User::class),
            type($request->password)->asString()
        );

        if (! $confirmed) {
            throw ValidationException::withMessages([
                'password' => __('The password is incorrect.'),
            ]);
        }

        $guard->logoutOtherDevices(type($request->password)->asString());

        BrowserSessions::logoutOtherBrowserSessions();

        return back(303);
    }
}
