<?php

declare(strict_types=1);

namespace App\Actions\BankAccounts;

use App\Data\Finance\BankConnectionCreateData;
use App\Jobs\SyncBankAccounts;
use App\Models\BankConnection;
use Illuminate\Support\Facades\Context;

final readonly class CreateBankConnection
{
    public function __construct(
        private CreateAccount $action,
    ) {}

    /**
     * Create bank connections after the user has selected the accounts they want to connect.
     */
    public function create(BankConnectionCreateData $payload): void
    {
        $account = $payload->accounts[0];

        $bankConnection = BankConnection::updateOrCreate(
            [
                'institution_id' => $account->institutionId,
                'workspace_id' => Context::get('currentWorkspace'),
            ],
            [
                'institution_logo_url' => $account->institutionLogoUrl,
                'institution_name' => $account->institutionName,
                'provider_connection_id' => $payload->providerConnectionId,
                'provider_type' => $payload->providerType,
                'access_token' => $payload->accessToken,
                'token_expires_at' => $account->tokenExpiresAt,
            ],
        );

        foreach ($payload->accounts as $account) {
            $this->action->create([
                'bank_connection_id' => $bankConnection->id,
                'name' => $account->name,
                'currency_code' => $account->currency,
                'initial_balance' => $account->balance,
                'is_default' => false,
                'external_id' => $account->accountId,
                'type' => $account->type,
                'subtype' => $account->subtype,
            ]);
        }

        // Trigger an initial sync of the accounts
        // This will run immediately for the first time
        // The scheduled job will run daily at the same time the connection was created
        $workspaceId = type(Context::get('currentWorkspace'))->asInt();
        SyncBankAccounts::dispatch(
            $workspaceId,
            $bankConnection->id,
            true
        )->onQueue('financial');
    }
}
