<?php

declare(strict_types=1);

namespace App\Data\Currency;

use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\CamelCaseMapper;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
#[MapName(SnakeCaseMapper::class, CamelCaseMapper::class)]
final class ArgentinaDollarItemData extends Data
{
    public function __construct(
        public readonly int|float $valueAvg,
        public readonly int|float $valueSell,
        public readonly int|float $valueBuy,
    ) {}
}
