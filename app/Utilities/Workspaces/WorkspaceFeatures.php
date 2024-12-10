<?php

declare(strict_types=1);

namespace App\Utilities\Workspaces;

final class WorkspaceFeatures
{
    /**
     * Determine if the application is allowing profile photo uploads.
     */
    public static function managesProfilePhotos(): bool
    {
        return self::enabled(self::profilePhotos());
    }

    /**
     * Determine if the given feature is enabled.
     */
    public static function enabled(string $feature): bool
    {
        return in_array($feature, type(config('workspaces.features', []))->asArray());
    }

    /**
     * Enable the profile photo upload feature.
     */
    public static function profilePhotos(): string
    {
        return 'profile-photos';
    }

    /**
     * Determine if the application is using any API features.
     */
    public static function hasApiFeatures(): bool
    {
        return self::enabled(self::api());
    }

    /**
     * Enable the API feature.
     */
    public static function api(): string
    {
        return 'api';
    }

    /**
     * Determine if the application is using any workspace features.
     */
    public static function hasWorkspaceFeatures(): bool
    {
        return self::enabled(self::workspaces());
    }

    /**
     * Enable the workspaces feature.
     *
     * @param  array<string, mixed>  $options
     */
    public static function workspaces(array $options = []): string
    {
        if ($options !== []) {
            config(['workspaces-options.workspaces' => $options]);
        }

        return 'workspaces';
    }

    /**
     * Determine if invitations are sent to workspace members.
     */
    public static function sendsWorkspaceInvitations(): bool
    {
        return self::optionEnabled(self::workspaces(), 'invitations');
    }

    /**
     * Determine if the feature is enabled and has a given option enabled.
     */
    public static function optionEnabled(string $feature, string $option): bool
    {
        return self::enabled($feature) &&
            config("workspaces-options.{$feature}.{$option}") === true;
    }

    /**
     * Determine if the application has terms of service / privacy policy confirmation enabled.
     */
    public static function hasTermsAndPrivacyPolicyFeature(): bool
    {
        return self::enabled(self::termsAndPrivacyPolicy());
    }

    /**
     * Enable the terms of service and privacy policy feature.
     */
    public static function termsAndPrivacyPolicy(): string
    {
        return 'terms';
    }

    /**
     * Determine if the application is using any account deletion features.
     */
    public static function hasAccountDeletionFeatures(): bool
    {
        return self::enabled(self::accountDeletion());
    }

    /**
     * Enable the account deletion feature.
     */
    public static function accountDeletion(): string
    {
        return 'account-deletion';
    }
}
