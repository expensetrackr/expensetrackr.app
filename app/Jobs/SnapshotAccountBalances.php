<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Actions\AccountBalances\SnapshotBalanceAction;
use App\Models\Account;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Throwable;

final class SnapshotAccountBalances implements ShouldBeUnique, ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     */
    public int $tries = 3;

    /**
     * The number of seconds to wait before retrying the job.
     */
    public int $backoff = 60;

    /**
     * The number of seconds after which the job's unique lock will be released.
     */
    public int $uniqueFor = 3600;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * The unique ID of the job.
     */
    public function uniqueId(): string
    {
        return 'save_account_balances_'.now()->toDateString();
    }

    /**
     * Execute the job.
     */
    public function handle(SnapshotBalanceAction $action): void
    {
        try {
            Account::query()
                ->select(['id', 'current_balance', 'workspace_id'])
                ->chunk(1000, function ($accounts) use ($action): void {
                    $action->handle($accounts);
                });
        } catch (Throwable $e) {
            $this->fail("Failed to save daily balances: {$e->getMessage()}");
        }
    }
}
