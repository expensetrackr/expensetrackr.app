<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\Workspaces\ValidateWorkspaceDeletion;
use App\Concerns\RedirectsActions;
use App\Contracts\CreatesWorkspaces;
use App\Contracts\DeletesWorkspaces;
use App\Contracts\UpdatesWorkspaceNames;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

final class WorkspaceController extends Controller
{
    use RedirectsActions;

    /**
     * Show the workspace management screen.
     */
    public function show(int $workspaceId): Response
    {
        $workspace = Workspace::findOrFail($workspaceId);

        Gate::authorize('view', $workspace);

        return Inertia::render('workspaces/show', [
            'workspace' => $workspace->load('owner', 'users', 'workspaceInvitations'),
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
    public function store(Request $request): Application|RedirectResponse|\Illuminate\Http\Response|Redirector|Response
    {
        $creator = app(CreatesWorkspaces::class);

        $creator->create(type($request->user())->as(User::class), $request->all());

        return $this->redirectPath($creator);
    }

    /**
     * Show the workspace creation screen.
     *
     * @return Response
     */
    public function create()
    {
        Gate::authorize('create', Workspace::class);

        return Inertia::render('Workspaces/Create');
    }

    /**
     * Update the given workspaces name.
     */
    public function update(Request $request, int $workspaceId): RedirectResponse
    {
        $workspace = Workspace::findOrFail($workspaceId);

        app(UpdatesWorkspaceNames::class)->update(type($request->user())->as(User::class), $workspace, $request->all());

        return back(303);
    }

    /**
     * Delete the given workspace.
     */
    public function destroy(Request $request, int $workspaceId): Application|RedirectResponse|\Illuminate\Http\Response|Redirector|Response
    {
        $workspace = Workspace::findOrFail($workspaceId);

        app(ValidateWorkspaceDeletion::class)->validate(type($request->user())->as(User::class), $workspace);

        $deleter = app(DeletesWorkspaces::class);

        $deleter->delete($workspace);

        return $this->redirectPath($deleter);
    }
}
