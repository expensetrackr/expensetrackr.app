<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StoreAccountStepRequest;
use App\Models\Account;
use App\Services\AccountWizardService;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;
use InvalidArgumentException;

final class AccountController extends Controller
{
    /**
     * Create a new instance of the Account controller.
     */
    public function __construct(
        private readonly AccountWizardService $wizardService,
    ) {}

    /**
     * Display all accounts.
     */
    public function index(): Response
    {
        Gate::authorize('viewAny', Account::class);

        return Inertia::render('accounts/index', [
            'accounts' => Account::all()->sortBy('name'),
        ]);
    }

    /**
     * Create a new account
     */
    public function create(): Response
    {
        Gate::authorize('create', Account::class);

        return Inertia::render('accounts/create/index');
    }

    /**
     * Handle form submission for each step
     *
     * @throws AuthorizationException
     */
    public function store(StoreAccountStepRequest $request, string $step): RedirectResponse
    {
        Gate::authorize('create', Account::class);

        try {
            $this->wizardService->validateStep($step);
        } catch (InvalidArgumentException $e) {
            return redirect()->route('accounts.create', ['step' => 'details'])
                ->with('toast', ['type' => 'error', 'message' => $e->getMessage()]);
        }

        $validated = $request->validated();
        $this->wizardService->storeStepData($request, $step, $validated);

        if ($step === AccountWizardService::STEP_REVIEW) {
            try {
                $this->wizardService->createAccount($request);
                $this->wizardService->clearWizardData($request);

                return redirect()->route('accounts.index')
                    ->with('success', 'Account created successfully.');
            } catch (Exception) {
                return redirect()->back()->with('error', 'Failed to create account. Please try again.');
            }
        }

        try {
            $nextStep = $this->wizardService->getNextStep($step);

            return redirect()->route('accounts.create', ['step' => $nextStep]);
        } catch (InvalidArgumentException $e) {
            return redirect()->back()
                ->with('toast', ['type' => 'error', 'message' => $e->getMessage()]);
        }
    }
}
