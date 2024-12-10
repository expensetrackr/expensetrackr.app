<?php

declare(strict_types=1);

namespace App\Models;

use App\Casts\MoneyCast;
use App\Concerns\Blamable;
use App\Concerns\WorkspaceOwned;
use App\Enums\AccountSubtype;
use Carbon\CarbonImmutable;
use Database\Factories\AccountFactory;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Spatie\PrefixedIds\Models\Concerns\HasPrefixedId;

/**
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property string $currency_code
 * @property string $initial_balance
 * @property string $current_balance
 * @property bool $is_default
 * @property string $public_id
 * @property int $workspace_id
 * @property AccountSubtype|null $subtype
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property CarbonImmutable|null $created_at
 * @property CarbonImmutable|null $updated_at
 * @property string $accountable_type
 * @property int $accountable_id
 * @property-read Model|Eloquent $accountable
 * @property-read User|null $createdBy
 * @property-read User|null $updatedBy
 * @property-read Workspace $workspace
 * @property-read string|null $prefixed_id
 *
 * @method static AccountFactory factory($count = null, $state = [])
 * @method static Builder<static>|Account newModelQuery()
 * @method static Builder<static>|Account newQuery()
 * @method static Builder<static>|Account query()
 * @method static Builder<static>|Account whereAccountableId($value)
 * @method static Builder<static>|Account whereAccountableType($value)
 * @method static Builder<static>|Account whereCreatedAt($value)
 * @method static Builder<static>|Account whereCreatedBy($value)
 * @method static Builder<static>|Account whereCurrencyCode($value)
 * @method static Builder<static>|Account whereCurrentBalance($value)
 * @method static Builder<static>|Account whereDescription($value)
 * @method static Builder<static>|Account whereId($value)
 * @method static Builder<static>|Account whereInitialBalance($value)
 * @method static Builder<static>|Account whereIsDefault($value)
 * @method static Builder<static>|Account whereName($value)
 * @method static Builder<static>|Account wherePublicId($value)
 * @method static Builder<static>|Account whereUpdatedAt($value)
 * @method static Builder<static>|Account whereUpdatedBy($value)
 * @method static Builder<static>|Account whereWorkspaceId($value)
 * @method static Builder<static>|Account whereSubtype($value)
 *
 * @mixin Eloquent
 */
final class Account extends Model
{
    /** @use HasFactory<AccountFactory> */
    use Blamable, HasFactory, HasPrefixedId, WorkspaceOwned;

    /**
     * The accountable model.
     *
     * @return MorphTo<Model, covariant $this>
     */
    public function accountable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'initial_balance' => MoneyCast::class,
            'current_balance' => MoneyCast::class,
            'subtype' => AccountSubtype::class,
        ];
    }
}
