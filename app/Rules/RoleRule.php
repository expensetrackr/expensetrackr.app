<?php

declare(strict_types=1);

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Spatie\Permission\Models\Role as SpatieRole;

final class RoleRule implements ValidationRule
{
    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! in_array($value, array_keys(SpatieRole::all()->pluck('name')->toArray()))) {
            $fail(__('The :attribute must be a valid role.'));
        }
    }
}
