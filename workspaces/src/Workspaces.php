<?php

declare(strict_types=1);

namespace Workspaces;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Workspaces\Contracts\AddsWorkspaceMembers;
use Workspaces\Contracts\CreatesWorkspaces;
use Workspaces\Contracts\DeletesUsers;
use Workspaces\Contracts\DeletesWorkspaces;
use Workspaces\Contracts\InvitesWorkspaceMembers;
use Workspaces\Contracts\RemovesWorkspaceMembers;
use Workspaces\Contracts\UpdatesWorkspaceNames;

final class Workspaces
{
    /**
     * Indicates if Workspaces routes will be registered.
     */
    public static bool $registersRoutes = true;

    /**
     * The roles that are available to assign to users.
     */
    public static array $roles = [];

    /**
     * The permissions that exist within the application.
     */
    public static array $permissions = [];

    /**
     * The default permissions that should be available to new entities.
     */
    public static array $defaultPermissions = [];

    /**
     * The user model that should be used by Workspaces.
     */
    public static string $userModel = 'App\\Models\\User';

    /**
     * The workspace model that should be used by Workspaces.
     */
    public static string $workspaceModel = 'App\\Models\\Workspace';

    /**
     * The membership model that should be used by Workspaces.
     */
    public static string $membershipModel = 'App\\Models\\Membership';

    /**
     * The workspace invitation model that should be used by Workspaces.
     */
    public static string $workspaceInvitationModel = 'App\\Models\\WorkspaceInvitation';

    /**
     * The Inertia manager instance.
     */
    public static ?InertiaManager $inertiaManager = null;

    /**
     * Determine if Workspaces has registered roles.
     */
    public static function hasRoles(): bool
    {
        return count(self::$roles) > 0;
    }

    /**
     * Find the role with the given key.
     */
    public static function findRole(string $key): ?Role
    {
        return self::$roles[$key] ?? null;
    }

    /**
     * Define a role.
     */
    public static function role(string $key, string $name, array $permissions): Role
    {
        self::$permissions = collect(array_merge(self::$permissions, $permissions))
            ->unique()
            ->sort()
            ->values()
            ->all();

        return tap(new Role($key, $name, $permissions), function ($role) use ($key) {
            static::$roles[$key] = $role;
        });
    }

    /**
     * Determine if any permissions have been registered with Workspaces.
     */
    public static function hasPermissions(): bool
    {
        return count(self::$permissions) > 0;
    }

    /**
     * Define the available API token permissions.
     */
    public static function permissions(array $permissions): self
    {
        self::$permissions = $permissions;

        return new self;
    }

    /**
     * Define the default permissions that should be available to new API tokens.
     */
    public static function defaultApiTokenPermissions(array $permissions): self
    {
        self::$defaultPermissions = $permissions;

        return new self;
    }

    /**
     * Return the permissions in the given list that are actually defined permissions for the application.
     */
    public static function validPermissions(array $permissions): array
    {
        return array_values(array_intersect($permissions, self::$permissions));
    }

    /**
     * Determine if Workspaces is managing profile photos.
     */
    public static function managesProfilePhotos(): bool
    {
        return Features::managesProfilePhotos();
    }

    /**
     * Determine if Workspaces is supporting API features.
     */
    public static function hasApiFeatures(): bool
    {
        return Features::hasApiFeatures();
    }

    /**
     * Determine if a given user model utilizes the "HasWorkspaces" trait.
     */
    public static function userHasWorkspaceFeatures(Model $user): bool
    {
        return (array_key_exists(HasWorkspaces::class, class_uses_recursive($user)) ||
                method_exists($user, 'currentWorkspace')) &&
            self::hasWorkspaceFeatures();
    }

    /**
     * Determine if Workspaces is supporting workspace features.
     */
    public static function hasWorkspaceFeatures(): bool
    {
        return Features::hasWorkspaceFeatures();
    }

    /**
     * Determine if the application is using the terms confirmation feature.
     */
    public static function hasTermsAndPrivacyPolicyFeature(): bool
    {
        return Features::hasTermsAndPrivacyPolicyFeature();
    }

    /**
     * Determine if the application is using any account deletion features.
     */
    public static function hasAccountDeletionFeatures(): bool
    {
        return Features::hasAccountDeletionFeatures();
    }

    /**
     * Find a user instance by the given ID.
     */
    public static function findUserByIdOrFail(int $id): mixed
    {
        return self::newUserModel()->where('id', $id)->firstOrFail();
    }

    /**
     * Get a new instance of the user model.
     */
    public static function newUserModel(): mixed
    {
        $model = self::userModel();

        return new $model;
    }

    /**
     * Get the name of the user model used by the application.
     */
    public static function userModel(): string
    {
        return self::$userModel;
    }

    /**
     * Find a user instance by the given email address or fail.
     */
    public static function findUserByEmailOrFail(string $email): mixed
    {
        return self::newUserModel()->where('email', $email)->firstOrFail();
    }

    /**
     * Specify the user model that should be used by Workspaces.
     */
    public static function useUserModel(string $model): self
    {
        self::$userModel = $model;

        return new self;
    }

    /**
     * Get a new instance of the workspace model.
     */
    public static function newWorkspaceModel(): mixed
    {
        $model = self::workspaceModel();

        return new $model;
    }

    /**
     * Get the name of the workspace model used by the application.
     */
    public static function workspaceModel(): string
    {
        return self::$workspaceModel;
    }

    /**
     * Specify the workspace model that should be used by Workspaces.
     *
     * @return static
     */
    public static function useWorkspaceModel(string $model): self
    {
        self::$workspaceModel = $model;

        return new self;
    }

    /**
     * Get the name of the membership model used by the application.
     */
    public static function membershipModel(): string
    {
        return self::$membershipModel;
    }

    /**
     * Specify the membership model that should be used by Workspaces.
     */
    public static function useMembershipModel(string $model): self
    {
        self::$membershipModel = $model;

        return new self;
    }

    /**
     * Get the name of the workspace invitation model used by the application.
     */
    public static function workspaceInvitationModel(): string
    {
        return self::$workspaceInvitationModel;
    }

    /**
     * Specify the workspace invitation model that should be used by Workspaces.
     */
    public static function useWorkspaceInvitationModel(string $model): self
    {
        self::$workspaceInvitationModel = $model;

        return new self;
    }

    /**
     * Register a class / callback that should be used to create workspaces.
     */
    public static function createWorkspacesUsing(string $class): void
    {
        app()->singleton(CreatesWorkspaces::class, $class);
    }

    /**
     * Register a class / callback that should be used to update workspace names.
     */
    public static function updateWorkspaceNamesUsing(string $class): void
    {
        app()->singleton(UpdatesWorkspaceNames::class, $class);
    }

    /**
     * Register a class / callback that should be used to add workspace members.
     */
    public static function addWorkspaceMembersUsing(string $class): void
    {
        app()->singleton(AddsWorkspaceMembers::class, $class);
    }

    /**
     * Register a class / callback that should be used to add workspace members.
     */
    public static function inviteWorkspaceMembersUsing(string $class): void
    {
        app()->singleton(InvitesWorkspaceMembers::class, $class);
    }

    /**
     * Register a class / callback that should be used to remove workspace members.
     */
    public static function removeWorkspaceMembersUsing(string $class): void
    {
        app()->singleton(RemovesWorkspaceMembers::class, $class);
    }

    /**
     * Register a class / callback that should be used to delete workspaces.
     */
    public static function deleteWorkspacesUsing(string $class): void
    {
        app()->singleton(DeletesWorkspaces::class, $class);
    }

    /**
     * Register a class / callback that should be used to delete users.
     */
    public static function deleteUsersUsing(string $class): void
    {
        app()->singleton(DeletesUsers::class, $class);
    }

    /**
     * Manage Workspaces Inertia settings.
     */
    public static function inertia(): InertiaManager
    {
        if (is_null(self::$inertiaManager)) {
            self::$inertiaManager = new InertiaManager;
        }

        return self::$inertiaManager;
    }

    /**
     * Find the path to a localized Markdown resource.
     */
    public static function localizedMarkdownPath(string $name): ?string
    {
        $localName = preg_replace('#(\.md)$#i', '.'.app()->getLocale().'$1', $name);

        return Arr::first([
            resource_path('markdown/'.$localName),
            resource_path('markdown/'.$name),
        ], function ($path) {
            return file_exists($path);
        });
    }

    /**
     * Configure Workspaces to not register its routes.
     */
    public static function ignoreRoutes(): self
    {
        self::$registersRoutes = false;

        return new self;
    }
}