<?php

declare(strict_types=1);

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;

final class AddingWorkspaceMember
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
         * The workspace member being added.
         */
        public mixed $user
    ) {}
}
