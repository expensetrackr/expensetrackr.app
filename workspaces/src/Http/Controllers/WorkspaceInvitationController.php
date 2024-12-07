<?php

declare(strict_types=1);

namespace Workspaces\Http\Controllers;

use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Gate;
use Workspaces\Contracts\AddsWorkspaceMembers;
use Workspaces\Workspaces;

final class WorkspaceInvitationController extends Controller
{
    /**
     * Accept a workspace invitation.
     */
    public function accept(int $invitationId): RedirectResponse
    {
        $model = Workspaces::workspaceInvitationModel();

        $invitation = $model::whereKey($invitationId)->firstOrFail();

        app(AddsWorkspaceMembers::class)->add(
            type($invitation->workspace->owner)->as(User::class),
            $invitation->workspace,
            $invitation->email,
            $invitation->role
        );

        $invitation->delete();

        return redirect(type(config('fortify.home'))->asString())->banner(
            __('Great! You have accepted the invitation to join the :workspace workspace.', ['workspace' => $invitation->workspace->name]),
        );
    }

    /**
     * Cancel the given workspace invitation.
     */
    public function destroy(Request $request, int $invitationId): RedirectResponse
    {
        $model = Workspaces::workspaceInvitationModel();

        $invitation = $model::whereKey($invitationId)->firstOrFail();

        if (! Gate::forUser($request->user())->check('removeWorkspaceMember', $invitation->workspace)) {
            throw new AuthorizationException;
        }

        $invitation->delete();

        return back(303);
    }
}
