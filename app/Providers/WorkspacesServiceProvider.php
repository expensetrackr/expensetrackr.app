<?php

declare(strict_types=1);

namespace App\Providers;

use App\Actions\Workspaces\AddWorkspaceMember;
use App\Actions\Workspaces\CreateWorkspace;
use App\Actions\Workspaces\DeleteUser;
use App\Actions\Workspaces\DeleteWorkspace;
use App\Actions\Workspaces\InviteWorkspaceMember;
use App\Actions\Workspaces\RemoveWorkspaceMember;
use App\Actions\Workspaces\UpdateWorkspaceName;
use Illuminate\Support\ServiceProvider;

final class WorkspacesServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        app()->singleton(CreateWorkspace::class);
        app()->singleton(UpdateWorkspaceName::class);
        app()->singleton(AddWorkspaceMember::class);
        app()->singleton(InviteWorkspaceMember::class);
        app()->singleton(RemoveWorkspaceMember::class);
        app()->singleton(DeleteWorkspace::class);
        app()->singleton(DeleteUser::class);
    }
}
