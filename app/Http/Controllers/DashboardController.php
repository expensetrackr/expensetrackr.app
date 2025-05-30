<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\User;
use App\Services\BalanceSheet;
use App\ValueObjects\Period;
use Illuminate\Container\Attributes\CurrentUser;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

final class DashboardController extends Controller
{
    public function __invoke(Request $request, #[CurrentUser] User $user): Response|RedirectResponse
    {
        if (! $user->currentWorkspace) {
            return to_route('dashboard')
                ->with('toast', [
                    'title' => 'No workspace selected',
                    'type' => 'error',
                ]);
        }

        $balanceSheet = new BalanceSheet($user->currentWorkspace);

        $request->query('q', '');

        /** @var array{period: string} */
        $totalBalancePeriod = $request->query('total_balance', [
            'period' => 'last-month',
        ]);
        $totalBalanceParams = match ($totalBalancePeriod['period']) {
            'last-month' => ['period' => Period::lastMonth(), 'interval' => 'day'],
            'last-6-months' => ['period' => Period::last6Months(), 'interval' => 'month'],
            'last-year' => ['period' => Period::lastYear(), 'interval' => 'month'],
            default => ['period' => Period::lastMonth(), 'interval' => 'day'],
        };

        return Inertia::render('dashboard', [
            'netWorth' => $balanceSheet->netWorth(),
            'series' => [
                'totalBalance' => $balanceSheet->netWorthSeries($totalBalanceParams['period'], $totalBalanceParams['interval']),
            ],
            'transactions' => Transaction::with(['category', 'merchant'])
                ->latest('dated_at')
                ->take(5)
                ->get()
                ->toResourceCollection(),
            // Handy for updating the table when anything from server side changes
            'requestId' => Str::uuid(),
        ]);
    }
}
