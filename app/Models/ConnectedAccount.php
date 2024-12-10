<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\CarbonImmutable;
use Database\Factories\ConnectedAccountFactory;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasTimestamps;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use JoelButcher\Socialstream\ConnectedAccount as SocialstreamConnectedAccount;
use JoelButcher\Socialstream\Events\ConnectedAccountCreated;
use JoelButcher\Socialstream\Events\ConnectedAccountDeleted;
use JoelButcher\Socialstream\Events\ConnectedAccountUpdated;

/**
 * @property int $id
 * @property int $user_id
 * @property string $provider
 * @property string $provider_id
 * @property string|null $name
 * @property string|null $nickname
 * @property string|null $email
 * @property string|null $telephone
 * @property string|null $avatar_path
 * @property string $token
 * @property string|null $secret
 * @property string|null $refresh_token
 * @property CarbonImmutable|null $expires_at
 * @property CarbonImmutable|null $created_at
 * @property CarbonImmutable|null $updated_at
 * @property-read User|null $user
 *
 * @method static ConnectedAccountFactory factory($count = null, $state = [])
 * @method static Builder<static>|ConnectedAccount newModelQuery()
 * @method static Builder<static>|ConnectedAccount newQuery()
 * @method static Builder<static>|ConnectedAccount query()
 * @method static Builder<static>|ConnectedAccount whereAvatarPath($value)
 * @method static Builder<static>|ConnectedAccount whereCreatedAt($value)
 * @method static Builder<static>|ConnectedAccount whereEmail($value)
 * @method static Builder<static>|ConnectedAccount whereExpiresAt($value)
 * @method static Builder<static>|ConnectedAccount whereId($value)
 * @method static Builder<static>|ConnectedAccount whereName($value)
 * @method static Builder<static>|ConnectedAccount whereNickname($value)
 * @method static Builder<static>|ConnectedAccount whereProvider($value)
 * @method static Builder<static>|ConnectedAccount whereProviderId($value)
 * @method static Builder<static>|ConnectedAccount whereRefreshToken($value)
 * @method static Builder<static>|ConnectedAccount whereSecret($value)
 * @method static Builder<static>|ConnectedAccount whereTelephone($value)
 * @method static Builder<static>|ConnectedAccount whereToken($value)
 * @method static Builder<static>|ConnectedAccount whereUpdatedAt($value)
 * @method static Builder<static>|ConnectedAccount whereUserId($value)
 *
 * @mixin Eloquent
 */
final class ConnectedAccount extends SocialstreamConnectedAccount
{
    /** @use HasFactory<ConnectedAccountFactory> */
    use HasFactory, HasTimestamps;

    /**
     * The event map for the model.
     *
     * @var array<string, string>
     */
    protected $dispatchesEvents = [
        'created' => ConnectedAccountCreated::class,
        'updated' => ConnectedAccountUpdated::class,
        'deleted' => ConnectedAccountDeleted::class,
    ];

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
        ];
    }
}
