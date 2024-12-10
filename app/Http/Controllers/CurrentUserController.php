<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Contracts\DeletesUsers;
use App\Models\User;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Laravel\Fortify\Actions\ConfirmPassword;
use Symfony\Component\HttpFoundation\Response;

final class CurrentUserController extends Controller
{
    /**
     * Delete the current user.
     */
    public function destroy(Request $request, StatefulGuard $guard): Response
    {
        $user = type($request->user())->as(User::class);
        $confirmed = app(ConfirmPassword::class)(
            $guard,
            $user,
            type($request->password)->asString()
        );

        if (! $confirmed) {
            throw ValidationException::withMessages([
                'password' => __('The password is incorrect.'),
            ]);
        }

        app(DeletesUsers::class)->delete(type($user->fresh())->as(User::class));

        $guard->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Inertia::location(url('/'));
    }
}
