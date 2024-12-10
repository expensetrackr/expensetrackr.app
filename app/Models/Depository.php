<?php

declare(strict_types=1);

namespace App\Models;

use App\Concerns\Accountable;
use Carbon\CarbonImmutable;
use Database\Factories\DepositoryFactory;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property CarbonImmutable|null $created_at
 * @property CarbonImmutable|null $updated_at
 * @property-read Account|null $account
 *
 * @method static DepositoryFactory factory($count = null, $state = [])
 * @method static Builder<static>|Depository newModelQuery()
 * @method static Builder<static>|Depository newQuery()
 * @method static Builder<static>|Depository query()
 * @method static Builder<static>|Depository whereCreatedAt($value)
 * @method static Builder<static>|Depository whereId($value)
 * @method static Builder<static>|Depository whereUpdatedAt($value)
 *
 * @mixin Eloquent
 */
final class Depository extends Model
{
    /** @use HasFactory<DepositoryFactory> */
    use Accountable, HasFactory;
}
