<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\ConnectedAccount;
use App\Models\User;
use App\Models\Workspace;
use App\Utilities\Workspaces\WorkspaceFeatures;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use JoelButcher\Socialstream\Providers;

/** @extends Factory<User> */
final class UserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'two_factor_secret' => null,
            'two_factor_recovery_codes' => null,
            'remember_token' => Str::random(10),
            'profile_photo_path' => null,
            'current_workspace_id' => null,
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): self
    {
        return $this->state(fn (array $attributes): array => [
            'email_verified_at' => null,
        ]);
    }

    /**
     * Indicate that the user should have a personal workspace.
     */
    public function withPersonalWorkspace(?callable $callback = null): self
    {
        if (! WorkspaceFeatures::hasWorkspaceFeatures()) {
            return $this->state([]);
        }

        return $this->has(
            Workspace::factory()
                ->state(fn (array $attributes, User $user): array => [ // @phpstan-ignore-line
                    'name' => $user->name.'\'s Workspace',
                    'user_id' => $user->id,
                    'personal_workspace' => true,
                ])
                ->when(is_callable($callback), $callback),
            'ownedWorkspaces'
        );
    }

    /**
     * Indicate that the user should have a connected account for the given provider.
     */
    public function withConnectedAccount(string $provider, ?callable $callback = null): self
    {
        if (! Providers::enabled($provider)) {
            return $this->state([]);
        }

        return $this->has(
            ConnectedAccount::factory()
                ->state(fn (array $attributes, User $user): array => [ // @phpstan-ignore-line
                    'provider' => $provider,
                    'user_id' => $user->id,
                ])
                ->when(is_callable($callback), $callback),
            'ownedWorkspaces'
        );
    }
}
