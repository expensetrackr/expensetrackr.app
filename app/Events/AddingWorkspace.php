<?php

declare(strict_types=1);

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;

final class AddingWorkspace
{
    use Dispatchable;

    /**
     * Create a new event instance.
     */
    public function __construct(
        /**
         * The workspace owner.
         */
        public mixed $owner
    ) {}
}
