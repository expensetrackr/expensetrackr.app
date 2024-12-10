<?php

declare(strict_types=1);

namespace App\Actions\Fortify;

use App\Models\User;
use App\Models\Workspace;
use App\Utilities\Workspaces\WorkspaceFeatures;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;

final class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Create a newly registered user.
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input): User
    {
        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => $this->passwordRules(),
            'terms' => WorkspaceFeatures::hasTermsAndPrivacyPolicyFeature() ? ['accepted', 'required'] : '',
        ])->validate();

        $user = new User();
        $user = $user->create([
            'name' => $input['name'],
            'email' => $input['email'],
            'password' => Hash::make($input['password']),
        ]);

        return DB::transaction(fn () => tap($user, function (User $user): void {
            $workspace = $this->createWorkspace($user);

            setPermissionsTeamId($workspace->id);

            $user->assignRole('workspace admin');
        }));
    }

    /**
     * Create a personal workspace for the user.
     */
    private function createWorkspace(User $user): Workspace
    {
        return $user->ownedWorkspaces()->forceCreate([
            'user_id' => $user->id,
            'name' => explode(' ', $user->name, 2)[0]."'s Workspace",
            'personal_workspace' => true,
        ]);
    }
}
