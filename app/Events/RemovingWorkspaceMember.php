<?php

declare(strict_types=1);

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;

final class RemovingWorkspaceMember
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
         * The workspace member being removed.
         */
        public mixed $user
    ) {}
}
