<?php

declare(strict_types=1);

namespace App\Actions\Workspaces;

use App\Contracts\RemovesWorkspaceMembers;
use App\Events\WorkspaceMemberRemoved;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;

final class RemoveWorkspaceMember implements RemovesWorkspaceMembers
{
    /**
     * Remove the workspace member from the given workspace.
     */
    public function remove(User $user, Workspace $workspace, User $workspaceMember): void
    {
        $this->authorize($user, $workspace, $workspaceMember);

        $this->ensureUserDoesNotOwnWorkspace($workspaceMember, $workspace);

        $workspace->removeMember($workspaceMember);

        WorkspaceMemberRemoved::dispatch($workspace, $workspaceMember);
    }

    /**
     * Authorize that the user can remove the workspace member.
     */
    private function authorize(User $user, Workspace $workspace, User $workspaceMember): void
    {
        if (
            ! Gate::forUser($user)->check('removeWorkspaceMember', $workspace) &&
            $user->id !== $workspaceMember->id
        ) {
            throw new AuthorizationException;
        }
    }

    /**
     * Ensure that the currently authenticated user does not own the workspace.
     */
    private function ensureUserDoesNotOwnWorkspace(User $workspaceMember, Workspace $workspace): void
    {
        if ($workspaceMember->id === $workspace->owner->id) { // @phpstan-ignore-line
            throw ValidationException::withMessages([
                'workspace' => [__('You may not leave a workspace that you created.')],
            ])->errorBag('removeWorkspaceMember');
        }
    }
}
