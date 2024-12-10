<?php

declare(strict_types=1);

namespace App\Models;

use App\Concerns\Accountable;
use Carbon\CarbonImmutable;
use Database\Factories\OtherAssetFactory;
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
 * @method static OtherAssetFactory factory($count = null, $state = [])
 * @method static Builder<static>|OtherAsset newModelQuery()
 * @method static Builder<static>|OtherAsset newQuery()
 * @method static Builder<static>|OtherAsset query()
 * @method static Builder<static>|OtherAsset whereCreatedAt($value)
 * @method static Builder<static>|OtherAsset whereId($value)
 * @method static Builder<static>|OtherAsset whereUpdatedAt($value)
 *
 * @mixin Eloquent
 */
final class OtherAsset extends Model
{
    /** @use HasFactory<OtherAssetFactory> */
    use Accountable, HasFactory;
}
