<?php

declare(strict_types=1);

namespace App\Actions\Workspaces;

use App\Models\User;
use App\Models\Workspace;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Workspaces\Contracts\CreatesWorkspaces;
use Workspaces\Events\AddingWorkspace;

final class CreateWorkspace implements CreatesWorkspaces
{
    /**
     * Validate and create a new workspace for the given user.
     *
     * @param  array<string, string>  $input
     */
    public function create(User $user, array $input): Workspace
    {
        Gate::forUser($user)->authorize('create', new Workspace);

        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
        ])->validateWithBag('createWorkspace');

        AddingWorkspace::dispatch($user);

        $user->switchWorkspace($workspace = $user->ownedWorkspaces()->create([
            'name' => $input['name'],
            'personal_workspace' => false,
        ]));

        return $workspace;
    }
}