<?php

declare(strict_types=1);

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;

final class InvitingWorkspaceMember
{
    use Dispatchable;

    /**
     * Create a new event instance.
     */
    public function __construct(
        /**
         * The workspace instance.
         */
        public mixed $workspace,
        /**
         * The email address of the invitee.
         */
        public mixed $email,
        /**
         * The role of the invitee.
         */
        public mixed $role
    ) {}
}
