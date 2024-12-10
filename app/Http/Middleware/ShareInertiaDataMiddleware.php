<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Models\Workspace;
use App\Utilities\Workspaces\WorkspaceFeatures;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Session;
use Inertia\Inertia;
use Laravel\Fortify\Features;

final class ShareInertiaDataMiddleware
{
    /**
     * Handle the incoming request.
     */
    public function handle(Request $request, callable $next): mixed
    {
        // @phpstan-ignore-next-line
        Inertia::share(array_filter([
            'workspaces' => function () use ($request): array {
                $user = $request->user();

                return [
                    'canCreateWorkspaces' => $user &&
                        Gate::forUser($user)->check('create', Workspace::class),
                    'canManageTwoFactorAuthentication' => Features::canManageTwoFactorAuthentication(),
                    'canUpdatePassword' => Features::enabled(Features::updatePasswords()),
                    'canUpdateProfileInformation' => Features::canUpdateProfileInformation(),
                    'hasEmailVerification' => Features::enabled(Features::emailVerification()),
                    'flash' => $request->session()->get('flash', []),
                    'hasAccountDeletionFeatures' => WorkspaceFeatures::hasAccountDeletionFeatures(),
                    'hasApiFeatures' => WorkspaceFeatures::hasApiFeatures(),
                    'hasWorkspaceFeatures' => WorkspaceFeatures::hasWorkspaceFeatures(),
                    'hasTermsAndPrivacyPolicyFeature' => WorkspaceFeatures::hasTermsAndPrivacyPolicyFeature(),
                    'managesProfilePhotos' => WorkspaceFeatures::managesProfilePhotos(),
                ];
            },
            'auth' => [
                'user' => function () use ($request): ?array {
                    if (! $user = $request->user()) {
                        return null;
                    }

                    $user->currentWorkspace; // @phpstan-ignore-line

                    return array_merge($user->toArray(), [
                        'all_workspaces' => $user->allWorkspaces()->values(),
                        'two_factor_enabled' => Features::enabled(Features::twoFactorAuthentication())
                            && ! is_null($user->two_factor_secret),
                    ]);
                },
            ],
            'errorBags' => function () {
                return collect(optional(Session::get('errors'))->getBags() ?: [])->mapWithKeys(fn ($bag, $key) => [$key => $bag->messages()])->all();  // @phpstan-ignore-line
            },
        ]));

        return $next($request);
    }
}
