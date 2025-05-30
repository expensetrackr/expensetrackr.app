<?php

declare(strict_types=1);

namespace App\Data\Banking;

use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\CamelCaseMapper;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
#[MapName(SnakeCaseMapper::class, CamelCaseMapper::class)]
final class TellerAccountBalanceData extends Data
{
    public function __construct(
        /**
         * The id of the account the account balances belong to.
         */
        public readonly string $accountId,
        /**
         * The account's ledger balance. The ledger balance is the total amount of funds in the account.
         */
        public readonly ?string $ledger,
        /**
         * The account's available balance. The available balance is the ledger balance net any pending inflows or outflows.
         */
        public readonly ?string $available,
    ) {}
}
