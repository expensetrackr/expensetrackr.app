<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\Workspaces\CreateWorkspace;
use App\Actions\Workspaces\DeleteWorkspace;
use App\Actions\Workspaces\UpdateWorkspace;
use App\Actions\Workspaces\ValidateWorkspaceDeletion;
use App\Concerns\RedirectsActions;
use App\Data\Workspace\WorkspaceData;
use App\Http\Requests\CreateWorkspaceRequest;
use App\Http\Requests\UpdateWorkspaceRequest;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Container\Attributes\CurrentUser;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\Permission\Models\Role;

final class WorkspaceController extends Controller
{
    use RedirectsActions;

    /**
     * Show the workspace management screen.
     */
    public function show(Request $request, Workspace $workspace): RedirectResponse|Response
    {
        if (! Gate::forUser($request->user())->check('view', $workspace)) {
            return to_route('dashboard');
        }

        return Inertia::render('workspaces/show', [
            'workspace' => WorkspaceData::fromModel($workspace),
            'availableRoles' => Role::get(),
            'permissions' => [
                'canAddWorkspaceMembers' => Gate::check('addWorkspaceMember', $workspace),
                'canDeleteWorkspace' => Gate::check('delete', $workspace),
                'canRemoveWorkspaceMembers' => Gate::check('removeWorkspaceMember', $workspace),
                'canUpdateWorkspace' => Gate::check('update', $workspace),
                'canUpdateWorkspaceMembers' => Gate::check('updateWorkspaceMember', $workspace),
            ],
        ]);
    }

    /**
     * Create a new workspace.
     */
    public function store(CreateWorkspaceRequest $request, CreateWorkspace $action): Application|RedirectResponse|\Illuminate\Http\Response|Redirector|Response
    {
        $action->handle(type($request->user())->as(User::class), $request->validated());

        return $this->redirectPath($action);
    }

    /**
     * Show the workspace creation screen.
     *
     * @return Response
     */
    public function create(Request $request): RedirectResponse|Response
    {
        if (! Gate::forUser($request->user())->check('create', Workspace::class)) {
            return to_route('dashboard');
        }

        return Inertia::render('Workspaces/Create');
    }

    /**
     * Update the given workspaces name.
     */
    public function update(UpdateWorkspaceRequest $request, Workspace $workspace, #[CurrentUser] User $user, UpdateWorkspace $action): RedirectResponse
    {
        $action->handle($user, $workspace, $request->validated());

        return back(303);
    }

    /**
     * Delete the given workspace.
     */
    public function destroy(Request $request, Workspace $workspace, DeleteWorkspace $action): Application|RedirectResponse|\Illuminate\Http\Response|Redirector|Response
    {
        if (! Gate::forUser($request->user())->check('delete', $workspace)) {
            return to_route('workspaces.show', $workspace);
        }

        resolve(ValidateWorkspaceDeletion::class)->validate(type($request->user())->as(User::class), $workspace);

        $action->handle($workspace);

        return $this->redirectPath($action);
    }
}
