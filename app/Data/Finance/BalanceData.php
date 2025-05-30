<?php

declare(strict_types=1);

namespace App\Data\Finance;

use App\Data\Banking\TellerAccountBalanceData;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
final class BalanceData extends Data
{
    public function __construct(
        public readonly string $currency,
        public readonly float $amount,
    ) {}

    public static function fromTeller(TellerAccountBalanceData $balance): self
    {
        return new self(
            currency: 'USD',
            amount: (float) $balance->available,
        );
    }
}
