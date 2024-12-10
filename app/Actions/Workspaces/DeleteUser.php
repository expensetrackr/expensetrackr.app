<?php

declare(strict_types=1);

namespace App\Actions\Workspaces;

use App\Contracts\DeletesUsers;
use App\Models\User;
use Illuminate\Support\Facades\DB;

final readonly class DeleteUser implements DeletesUsers
{
    /**
     * Create a new action instance.
     */
    public function __construct(
        /**
         * The workspace deleter implementation.
         */
        private DeleteWorkspace $deleteWorkspaces
    ) {}

    /**
     * Delete the given user.
     */
    public function delete(User $user): void
    {
        DB::transaction(function () use ($user): void {
            $this->deleteWorkspaces($user);
            $user->deleteProfilePhoto();
            $user->connectedAccounts->each->delete();
            $user->tokens->each->delete();
            $user->delete();
        });
    }

    /**
     * Delete the workspaces and workspace associations attached to the user.
     */
    private function deleteWorkspaces(User $user): void
    {
        $user->workspaces()->detach();

        $user->ownedWorkspaces->each(function ($workspace): void {
            $this->deleteWorkspaces->delete($workspace);
        });
    }
}
