<?php

declare(strict_types=1);

namespace App\Contracts;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User;

/**
 * @method void invite(User $user, Model $workspace, string $email, string $role = null)
 */
interface InvitesWorkspaceMembers
{
    //
}
