<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\CarbonImmutable;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $code
 * @property string $name
 * @property bool $available
 * @property CarbonImmutable|null $created_at
 * @property CarbonImmutable|null $updated_at
 *
 * @method static Builder<static>|CurrencyList newModelQuery()
 * @method static Builder<static>|CurrencyList newQuery()
 * @method static Builder<static>|CurrencyList query()
 * @method static Builder<static>|CurrencyList whereAvailable($value)
 * @method static Builder<static>|CurrencyList whereCode($value)
 * @method static Builder<static>|CurrencyList whereCreatedAt($value)
 * @method static Builder<static>|CurrencyList whereId($value)
 * @method static Builder<static>|CurrencyList whereName($value)
 * @method static Builder<static>|CurrencyList whereUpdatedAt($value)
 *
 * @mixin Eloquent
 */
final class CurrencyList extends Model
{
    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'available' => 'boolean',
        ];
    }
}
