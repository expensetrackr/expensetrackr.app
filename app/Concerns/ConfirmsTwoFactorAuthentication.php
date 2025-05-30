<?php

declare(strict_types=1);

namespace App\Concerns;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Fortify\Actions\DisableTwoFactorAuthentication;
use Laravel\Fortify\Features;

trait ConfirmsTwoFactorAuthentication
{
    /**
     * Validate the two-factor authentication state for the request.=
     */
    protected function validateTwoFactorAuthenticationState(Request $request): void
    {
        if (! Features::optionEnabled(Features::twoFactorAuthentication(), 'confirm')) {
            return;
        }

        $currentTime = time();

        // Notate totally disabled state in session...
        if ($this->twoFactorAuthenticationDisabled($request)) {
            $request->session()->put('two_factor_empty_at', $currentTime);
        }

        // If was previously totally disabled this session but is now confirming, notate time...
        if ($this->hasJustBegunConfirmingTwoFactorAuthentication($request)) {
            $request->session()->put('two_factor_confirming_at', $currentTime);
        }

        // If the profile is reloaded and is not confirmed but was previously in confirming state, disable...
        if ($this->neverFinishedConfirmingTwoFactorAuthentication($request, $currentTime)) {
            resolve(DisableTwoFactorAuthentication::class)(Auth::user());

            $request->session()->put('two_factor_empty_at', $currentTime);
            $request->session()->remove('two_factor_confirming_at');
        }
    }

    /**
     * Determine if two-factor authentication is totally disabled.
     */
    protected function twoFactorAuthenticationDisabled(Request $request): bool
    {
        return is_null($request->user()?->two_factor_secret) &&
            is_null($request->user()?->two_factor_confirmed_at);
    }

    /**
     * Determine if two-factor authentication is just now being confirmed within the last request cycle.
     */
    protected function hasJustBegunConfirmingTwoFactorAuthentication(Request $request): bool
    {
        return ! is_null($request->user()?->two_factor_secret) &&
            is_null($request->user()->two_factor_confirmed_at) &&
            $request->session()->has('two_factor_empty_at') &&
            is_null($request->session()->get('two_factor_confirming_at'));
    }

    /**
     * Determine if two-factor authentication was never totally confirmed once confirmation started.
     */
    protected function neverFinishedConfirmingTwoFactorAuthentication(Request $request, int $currentTime): bool
    {
        return ! array_key_exists('code', type($request->session()->getOldInput())->asArray()) &&
            is_null($request->user()?->two_factor_confirmed_at) &&
            $request->session()->get('two_factor_confirming_at', 0) !== $currentTime;
    }
}
