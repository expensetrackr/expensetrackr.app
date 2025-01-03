<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\Workspaces\AddWorkspaceMember;
use App\Actions\Workspaces\InviteWorkspaceMember;
use App\Actions\Workspaces\RemoveWorkspaceMember;
use App\Actions\Workspaces\UpdateWorkspaceMemberRole;
use App\Models\User;
use App\Models\Workspace;
use App\Utilities\Workspaces\WorkspaceFeatures;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

final class WorkspaceMemberController extends Controller
{
    /**
     * Add a new workspace member to a workspace.
     */
    public function store(Request $request, int $workspaceId): RedirectResponse
    {
        $workspace = Workspace::findOrFail($workspaceId);

        if (WorkspaceFeatures::sendsWorkspaceInvitations()) {
            app(InviteWorkspaceMember::class)->invite(
                type($request->user())->as(User::class),
                $workspace,
                in_array(type($request->email)->asString(), ['', '0'], true) ? '' : type($request->email)->asString(),
                type($request->role)->asString()
            );
        } else {
            app(AddWorkspaceMember::class)->add(
                type($request->user())->as(User::class),
                $workspace,
                in_array(type($request->email)->asString(), ['', '0'], true) ? '' : type($request->email)->asString(),
                type($request->role)->asString()
            );
        }

        return back(303);
    }

    /**
     * Update the given workspace member's role.
     */
    public function update(Request $request, int $workspaceId, int $userId): RedirectResponse
    {
        if ($request->has('role')) {
            app(UpdateWorkspaceMemberRole::class)->update(
                type($request->user())->as(User::class),
                Workspace::findOrFail($workspaceId),
                $userId,
                type($request->role)->asString()
            );
        }

        return back(303);
    }

    /**
     * Remove the given user from the given workspace.
     */
    public function destroy(Request $request, int $workspaceId, int $userId): RedirectResponse
    {
        $workspace = Workspace::findOrFail($workspaceId);

        app(RemoveWorkspaceMember::class)->remove(
            type($request->user())->as(User::class),
            $workspace,
            $user = User::whereId($userId)->firstOrFail()
        );

        if ($request->user()?->id === $user->id) {
            return redirect(type(config('fortify.home'))->asString());
        }

        return back(303);
    }
}
