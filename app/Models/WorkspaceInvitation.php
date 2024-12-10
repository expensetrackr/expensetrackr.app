<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\CarbonImmutable;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $workspace_id
 * @property string $email
 * @property string|null $role
 * @property CarbonImmutable|null $created_at
 * @property CarbonImmutable|null $updated_at
 * @property-read Workspace $workspace
 *
 * @method static Builder<static>|WorkspaceInvitation newModelQuery()
 * @method static Builder<static>|WorkspaceInvitation newQuery()
 * @method static Builder<static>|WorkspaceInvitation query()
 * @method static Builder<static>|WorkspaceInvitation whereCreatedAt($value)
 * @method static Builder<static>|WorkspaceInvitation whereEmail($value)
 * @method static Builder<static>|WorkspaceInvitation whereId($value)
 * @method static Builder<static>|WorkspaceInvitation whereRole($value)
 * @method static Builder<static>|WorkspaceInvitation whereUpdatedAt($value)
 * @method static Builder<static>|WorkspaceInvitation whereWorkspaceId($value)
 *
 * @mixin Eloquent
 */
final class WorkspaceInvitation extends Model
{
    /**
     * Get the workspace that the invitation belongs to.
     *
     * @return BelongsTo<Workspace, $this>
     */
    final public function workspace(): BelongsTo
    {
        return $this->belongsTo(Workspace::class);
    }
}
