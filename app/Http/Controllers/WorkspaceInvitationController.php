<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Contracts\AddsWorkspaceMembers;
use App\Models\User;
use App\Models\WorkspaceInvitation;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Gate;

final class WorkspaceInvitationController extends Controller
{
    /**
     * Accept a workspace invitation.
     */
    public function accept(int $invitationId): RedirectResponse
    {
        $invitation = WorkspaceInvitation::whereKey($invitationId)->firstOrFail();

        app(AddsWorkspaceMembers::class)->add(
            type($invitation->workspace->owner)->as(User::class),
            $invitation->workspace,
            $invitation->email,
            $invitation->role
        );

        $invitation->delete();

        return redirect(type(config('fortify.home'))->asString())
            ->with('toast', ['type' => 'success', 'message' => __('Great! You have accepted the invitation to join the :workspace workspace.', ['workspace' => $invitation->workspace->name])]);
    }

    /**
     * Cancel the given workspace invitation.
     */
    public function destroy(Request $request, int $invitationId): RedirectResponse
    {
        $invitation = WorkspaceInvitation::whereKey($invitationId)->firstOrFail();

        if (! Gate::forUser($request->user())->check('removeWorkspaceMember', $invitation->workspace)) {
            throw new AuthorizationException;
        }

        $invitation->delete();

        return back(303);
    }
}
