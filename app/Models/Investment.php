<?php

declare(strict_types=1);

namespace App\Models;

use App\Concerns\Accountable;
use Carbon\CarbonImmutable;
use Database\Factories\InvestmentFactory;
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
 * @method static InvestmentFactory factory($count = null, $state = [])
 * @method static Builder<static>|Investment newModelQuery()
 * @method static Builder<static>|Investment newQuery()
 * @method static Builder<static>|Investment query()
 * @method static Builder<static>|Investment whereCreatedAt($value)
 * @method static Builder<static>|Investment whereId($value)
 * @method static Builder<static>|Investment whereUpdatedAt($value)
 *
 * @mixin Eloquent
 */
final class Investment extends Model
{
    /** @use HasFactory<InvestmentFactory> */
    use Accountable, HasFactory;
}
