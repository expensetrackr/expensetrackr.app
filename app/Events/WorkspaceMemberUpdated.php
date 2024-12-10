<?php

declare(strict_types=1);

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;

final class WorkspaceMemberUpdated
{
    use Dispatchable;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(
        /**
         * The workspace instance.
         */
        public mixed $workspace,
        /**
         * The workspace member that was updated.
         */
        public mixed $user
    ) {}
}
