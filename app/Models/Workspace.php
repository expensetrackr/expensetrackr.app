<?php

declare(strict_types=1);

namespace App\Models;

use App\Events\WorkspaceCreated;
use App\Events\WorkspaceDeleted;
use App\Events\WorkspaceUpdated;
use Carbon\CarbonImmutable;
use Database\Factories\WorkspaceFactory;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property int $user_id
 * @property string $name
 * @property bool $personal_workspace
 * @property CarbonImmutable|null $created_at
 * @property CarbonImmutable|null $updated_at
 * @property-read User|null $owner
 * @property-read Membership|null $membership
 * @property-read Collection<int, User> $members
 * @property-read int|null $members_count
 * @property-read Collection<int, WorkspaceInvitation> $invitations
 * @property-read int|null $invitations_count
 *
 * @method static WorkspaceFactory factory($count = null, $state = [])
 * @method static Builder<static>|Workspace newModelQuery()
 * @method static Builder<static>|Workspace newQuery()
 * @method static Builder<static>|Workspace query()
 * @method static Builder<static>|Workspace whereCreatedAt($value)
 * @method static Builder<static>|Workspace whereId($value)
 * @method static Builder<static>|Workspace whereName($value)
 * @method static Builder<static>|Workspace wherePersonalWorkspace($value)
 * @method static Builder<static>|Workspace whereUpdatedAt($value)
 * @method static Builder<static>|Workspace whereUserId($value)
 *
 * @mixin Eloquent
 */
final class Workspace extends Model
{
    /** @use HasFactory<WorkspaceFactory> */
    use HasFactory;

    /**
     * The event map for the model.
     *
     * @var array<string, string>
     */
    protected $dispatchesEvents = [
        'created' => WorkspaceCreated::class,
        'updated' => WorkspaceUpdated::class,
        'deleted' => WorkspaceDeleted::class,
    ];

    /**
     * Determine if the given user belongs to the workspace.
     */
    public function hasUser(User $user): bool
    {
        if ($this->members->contains($user)) {
            return true;
        }

        return $user->ownsWorkspace($this);
    }

    /**
     * Determine if the given email address belongs to a user on the workspace.
     */
    public function hasUserWithEmail(string $email): bool
    {
        return $this->allMembers()->contains(fn ($user): bool => $user->email === $email);
    }

    /**
     * Get all the workspaces users including its owner.
     *
     * @return Collection<int, User>
     */
    public function allMembers(): Collection
    {
        return $this->members->merge([type($this->owner)->as(User::class)]);
    }

    /**
     * Get all the pending user invitations for the workspace.
     *
     * @return HasMany<WorkspaceInvitation, covariant $this>
     */
    public function invitations(): HasMany
    {
        return $this->hasMany(WorkspaceInvitation::class);
    }

    /**
     * Remove the given member from the workspace.
     */
    public function removeMember(User $user): void
    {
        if ($user->current_workspace_id === $this->id) {
            $user->forceFill([
                'current_workspace_id' => null,
            ])->save();
        }

        $this->members()->detach($user);
    }

    /**
     * Get all the users that belong to the workspace.
     *
     * @return BelongsToMany<User, covariant $this>
     */
    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class, Membership::class)
            ->withPivot('role')
            ->withTimestamps()
            ->as('membership');
    }

    /**
     * Purge all the workspaces resources.
     */
    public function purge(): void
    {
        $this->owner()->where('current_workspace_id', $this->id)
            ->update(['current_workspace_id' => null]);

        $this->members()->where('current_workspace_id', $this->id)
            ->update(['current_workspace_id' => null]);

        $this->members()->detach();

        $this->delete();
    }

    /**
     * Get the owner of the workspace.
     *
     * @return BelongsTo<User, covariant $this>
     */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'personal_workspace' => 'boolean',
        ];
    }
}
