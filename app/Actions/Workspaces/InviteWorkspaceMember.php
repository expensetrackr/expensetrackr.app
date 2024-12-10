<?php

declare(strict_types=1);

namespace App\Actions\Workspaces;

use App\Contracts\InvitesWorkspaceMembers;
use App\Events\InvitingWorkspaceMember;
use App\Mail\WorkspaceInvitationMail;
use App\Models\User;
use App\Models\Workspace;
use App\Rules\RoleRule;
use Closure;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Unique;

final class InviteWorkspaceMember implements InvitesWorkspaceMembers
{
    /**
     * Invite a new workspace member to the given workspace.
     */
    public function invite(User $user, Workspace $workspace, string $email, ?string $role = null): void
    {
        Gate::forUser($user)->authorize('addWorkspaceMember', $workspace);

        $this->validate($workspace, $email, $role);

        InvitingWorkspaceMember::dispatch($workspace, $email, $role);

        $invitation = $workspace->invitations()->create([
            'email' => $email,
            'role' => $role,
        ]);

        Mail::to($email)->send(new WorkspaceInvitationMail($invitation));
    }

    /**
     * Validate the invite member operation.
     */
    private function validate(Workspace $workspace, string $email, ?string $role): void
    {
        Validator::make([
            'email' => $email,
            'role' => $role,
        ], $this->rules($workspace), [
            'email.unique' => __('This user has already been invited to the workspace.'),
        ])->after(
            $this->ensureUserIsNotAlreadyOnWorkspace($workspace, $email)
        )->validateWithBag('addWorkspaceMember');
    }

    /**
     * Get the validation rules for inviting a workspace member.
     *
     * @return array<string, array<int, Unique|string|RoleRule>>
     */
    private function rules(Workspace $workspace): array
    {
        return [
            'email' => [
                'required',
                'email',
                Rule::unique('workspace_invitations')->where(function (Builder $query) use ($workspace): void {
                    $query->where('workspace_id', $workspace->id);
                }),
            ],
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
