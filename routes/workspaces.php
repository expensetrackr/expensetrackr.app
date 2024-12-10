<?php

declare(strict_types=1);

use App\Http\Controllers\ApiTokenController;
use App\Http\Controllers\CurrentUserController;
use App\Http\Controllers\CurrentWorkspaceController;
use App\Http\Controllers\OtherBrowserSessionsController;
use App\Http\Controllers\PrivacyAndSecurityController;
use App\Http\Controllers\PrivacyPolicyController;
use App\Http\Controllers\ProfilePhotoController;
use App\Http\Controllers\TermsOfServiceController;
use App\Http\Controllers\WorkspaceController;
use App\Http\Controllers\WorkspaceInvitationController;
use App\Http\Controllers\WorkspaceMemberController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => config('workspaces.middleware', ['web'])], function () {
    Route::get('/terms-of-service', [TermsOfServiceController::class, 'show'])->name('terms.show');
    Route::get('/privacy-policy', [PrivacyPolicyController::class, 'show'])->name('policy.show');

    $authMiddleware = config('workspaces.guard')
        ? 'auth:'.type(config('workspaces.guard'))->asString()
        : 'auth';

    $authSessionMiddleware = config('workspaces.auth_session', false)
        ? config('workspaces.auth_session')
        : null;

    Route::group(['middleware' => array_filter([$authMiddleware, $authSessionMiddleware])], function () {
        Route::prefix('settings')->group(function () {
            Route::get('/privacy-and-security', [PrivacyAndSecurityController::class, 'show'])->name('settings.privacy-and-security.show');
        });

        Route::delete('/user/other-browser-sessions', [OtherBrowserSessionsController::class, 'destroy'])
            ->name('other-browser-sessions.destroy');

        Route::delete('/user/profile-photo', [ProfilePhotoController::class, 'destroy'])
            ->name('current-user-photo.destroy');

        Route::delete('/user', [CurrentUserController::class, 'destroy'])
            ->name('current-user.destroy');

        Route::group(['middleware' => 'verified'], function () {
            Route::get('/user/api-tokens', [ApiTokenController::class, 'index'])->name('api-tokens.index');
            Route::post('/user/api-tokens', [ApiTokenController::class, 'store'])->name('api-tokens.store');
            Route::put('/user/api-tokens/{token}', [ApiTokenController::class, 'update'])->name('api-tokens.update');
            Route::delete('/user/api-tokens/{token}', [ApiTokenController::class, 'destroy'])->name('api-tokens.destroy');

            Route::get('/workspaces/create', [WorkspaceController::class, 'create'])->name('workspaces.create');
            Route::post('/workspaces', [WorkspaceController::class, 'store'])->name('workspaces.store');
            Route::get('/workspaces/{workspace}', [WorkspaceController::class, 'show'])->name('workspaces.show');
            Route::put('/workspaces/{workspace}', [WorkspaceController::class, 'update'])->name('workspaces.update');
            Route::delete('/workspaces/{workspace}', [WorkspaceController::class, 'destroy'])->name('workspaces.destroy');
            Route::put('/current-workspace', [CurrentWorkspaceController::class, 'update'])->name('current-workspace.update');
            Route::post('/workspaces/{workspace}/members', [WorkspaceMemberController::class, 'store'])->name('workspace-members.store');
            Route::put('/workspaces/{workspace}/members/{user}', [WorkspaceMemberController::class, 'update'])->name('workspace-members.update');
            Route::delete('/workspaces/{workspace}/members/{user}', [WorkspaceMemberController::class, 'destroy'])->name('workspace-members.destroy');

            Route::get('/workspace-invitations/{invitation}', [WorkspaceInvitationController::class, 'accept'])
                ->middleware(['signed'])
                ->name('workspace-invitations.accept');

            Route::delete('/workspace-invitations/{invitation}', [WorkspaceInvitationController::class, 'destroy'])
                ->name('workspace-invitations.destroy');
        });
    });
});
