<?php

declare(strict_types=1);

namespace App\Actions\Workspaces;

use App\Models\User;
use App\Models\Workspace;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;

final class ValidateWorkspaceDeletion
{
    /**
     * Validate that the workspace can be deleted by the given user.
     */
    public function validate(User $user, Workspace $workspace): void
    {
        Gate::forUser($user)->authorize('delete', $workspace);

        if ($workspace->personal_workspace) {
            throw ValidationException::withMessages([
                'workspace' => __('You may not delete your personal workspace.'),
            ])->errorBag('deleteWorkspace');
        }
    }
}
