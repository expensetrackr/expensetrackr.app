<?php

declare(strict_types=1);

namespace App\Actions\Workspaces;

use App\Events\WorkspaceMemberUpdated;
use App\Models\User;
use App\Models\Workspace;
use App\Rules\RoleRule;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;

final class UpdateWorkspaceMemberRole
{
    /**
     * Update the role for the given workspace member.
     */
    public function update(User $user, Workspace $workspace, int $workspaceMemberId, string $role): void
    {
        Gate::forUser($user)->authorize('updateWorkspaceMember', $workspace);

        Validator::make([
            'role' => $role,
        ], [
            'role' => ['required', 'string', new RoleRule],
        ])->validate();

        $workspace->members()->updateExistingPivot($workspaceMemberId, [
            'role' => $role,
        ]);

        WorkspaceMemberUpdated::dispatch($workspace->fresh(), User::whereId($workspaceMemberId)->firstOrFail());
    }
}
