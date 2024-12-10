<?php

declare(strict_types=1);

namespace App\Actions\Workspaces;

use App\Contracts\AddsWorkspaceMembers;
use App\Events\AddingWorkspaceMember;
use App\Events\WorkspaceMemberAdded;
use App\Models\User;
use App\Models\Workspace;
use App\Models\WorkspaceInvitation;
use App\Rules\RoleRule;
use Closure;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Unique;

final class AddWorkspaceMember implements AddsWorkspaceMembers
{
    /**
     * Add a new workspace member to the given workspace.
     */
    public function add(User $user, Workspace $workspace, string $email, ?string $role = null): void
    {
        Gate::forUser($user)->authorize('addWorkspaceMember', $workspace);

        $this->validate($workspace, $email, $role);

        $newWorkspaceMember = User::whereEmail($email)->firstOrFail();

        AddingWorkspaceMember::dispatch($workspace, $newWorkspaceMember);

        $workspace->members()->attach(
            $newWorkspaceMember,
            ['role' => $role]
        );

        WorkspaceMemberAdded::dispatch($workspace, $newWorkspaceMember);
    }

    /**
     * Validate the add member operation.
     */
    private function validate(Workspace $workspace, string $email, ?string $role): void
    {
        Validator::make([
            'email' => $email,
            'role' => $role,
        ], $this->rules($workspace), [
            'email.exists' => __('We were unable to find a registered user with this email address.'),
        ])->after(
            $this->ensureUserIsNotAlreadyOnWorkspace($workspace, $email)
        )->validateWithBag('addWorkspaceMember');
    }

    /**
     * Get the validation rules for adding a workspace member.
     *
     * @return array<string, array<int, RoleRule|Unique|string>>
     */
    private function rules(Workspace $workspace): array
    {
        return [
            'email' => ['required', 'email', Rule::unique(WorkspaceInvitation::class, 'email')->where('workspace_id', $workspace->id)],
            'role' => ['required', 'string', new RoleRule],
        ];
    }

    /**
     * Ensure that the user is not already on the workspace.
     */
    private function ensureUserIsNotAlreadyOnWorkspace(Workspace $workspace, string $email): Closure
    {
        return function ($validator) use ($workspace, $email): void {
            $validator->errors()->addIf(
                $workspace->hasUserWithEmail($email),
                'email',
                __('This user already belongs to the workspace.')
            );
        };
    }
}
