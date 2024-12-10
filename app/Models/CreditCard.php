<?php

declare(strict_types=1);

namespace App\Models;

use App\Concerns\Accountable;
use Carbon\CarbonImmutable;
use Database\Factories\CreditCardFactory;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $available_credit
 * @property string $minimum_payment
 * @property string $apr
 * @property string $annual_fee
 * @property int $expires_at
 * @property CarbonImmutable|null $created_at
 * @property CarbonImmutable|null $updated_at
 * @property-read Account|null $account
 *
 * @method static CreditCardFactory factory($count = null, $state = [])
 * @method static Builder<static>|CreditCard newModelQuery()
 * @method static Builder<static>|CreditCard newQuery()
 * @method static Builder<static>|CreditCard query()
 * @method static Builder<static>|CreditCard whereAnnualFee($value)
 * @method static Builder<static>|CreditCard whereApr($value)
 * @method static Builder<static>|CreditCard whereAvailableCredit($value)
 * @method static Builder<static>|CreditCard whereCreatedAt($value)
 * @method static Builder<static>|CreditCard whereExpiresAt($value)
 * @method static Builder<static>|CreditCard whereId($value)
 * @method static Builder<static>|CreditCard whereMinimumPayment($value)
 * @method static Builder<static>|CreditCard whereUpdatedAt($value)
 *
 * @mixin Eloquent
 */
final class CreditCard extends Model
{
    /** @use HasFactory<CreditCardFactory> */
    use Accountable, HasFactory;

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'expires_at' => 'timestamp',
        ];
    }
}
