<?php

declare(strict_types=1);

namespace App\Models;

use App\Casts\CurrencyRateCast;
use App\Concerns\WorkspaceOwned;
use App\Services\CurrencyService;
use App\Utilities\Currency\CurrencyAccessor;
use Carbon\CarbonImmutable;
use Database\Factories\CurrencyFactory;
use Eloquent;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $workspace_id
 * @property string $name
 * @property string $code
 * @property int $rate
 * @property int $precision
 * @property string $symbol
 * @property bool $symbol_first
 * @property string $decimal_mark
 * @property string|null $thousands_separator
 * @property bool $enabled
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property CarbonImmutable|null $created_at
 * @property CarbonImmutable|null $updated_at
 * @property-read float|null $live_rate
 * @property-read Workspace $workspace
 *
 * @method static CurrencyFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Currency newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Currency newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Currency query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Currency whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Currency whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Currency whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Currency whereDecimalMark($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Currency whereEnabled($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Currency whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Currency whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Currency wherePrecision($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Currency whereRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Currency whereSymbol($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Currency whereSymbolFirst($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Currency whereThousandsSeparator($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Currency whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Currency whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Currency whereWorkspaceId($value)
 *
 * @mixin Eloquent
 */
final class Currency extends Model
{
    /** @use HasFactory<CurrencyFactory> */
    use HasFactory, WorkspaceOwned;

    protected $appends = ['live_rate'];

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'rate' => CurrencyRateCast::class,
            'symbol_first' => 'boolean',
            'enabled' => 'boolean',
        ];
    }

    /**
     * @return Attribute<float|null, never>
     *
     * @phpstan-ignore method.unused
     */
    private function liveRate(): Attribute
    {
        return Attribute::get(static function (mixed $value, mixed $attributes = null): ?float {
            if (! is_array($attributes)) {
                return null;
            }

            $baseCurrency = CurrencyAccessor::getDefaultCurrency() ?? 'USD';
            $targetCurrency = type($attributes['code'])->asString();

            if ($baseCurrency === $targetCurrency) {
                return 1;
            }

            $currencyService = app(CurrencyService::class);
            $exchangeRate = $currencyService->getCachedExchangeRate($baseCurrency, $targetCurrency);

            return $exchangeRate ?? null;
        });
    }
}