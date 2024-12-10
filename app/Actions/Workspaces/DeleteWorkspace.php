<?php

declare(strict_types=1);

namespace App\Actions\Workspaces;

use App\Contracts\DeletesWorkspaces;
use App\Models\Workspace;

final class DeleteWorkspace implements DeletesWorkspaces
{
    /**
     * Delete the given workspace.
     */
    public function delete(Workspace $workspace): void
    {
        $workspace->purge();
    }
}
