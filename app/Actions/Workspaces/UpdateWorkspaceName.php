<?php

declare(strict_types=1);

namespace App\Actions\Workspaces;

use App\Contracts\UpdatesWorkspaceNames;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;

final class UpdateWorkspaceName implements UpdatesWorkspaceNames
{
    /**
     * Validate and update the given workspaces name.
     *
     * @param  array<string, mixed>  $input
     */
    public function update(User $user, Workspace $workspace, array $input): void
    {
        Gate::forUser($user)->authorize('update', $workspace);

        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
        ])->validateWithBag('updateWorkspaceName');

        $workspace->forceFill([
            'name' => $input['name'],
        ])->save();
    }
}
