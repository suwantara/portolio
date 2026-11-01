<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class AssetUrl implements ValidationRule
{
    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value) || $value === '' || trim($value) !== $value) {
            $fail(__('The :attribute field must be a valid URL or local path.'));

            return;
        }

        if (str_starts_with($value, '/') && ! str_starts_with($value, '//')) {
            if (preg_match('/[\s\\\\]/', $value) === 1) {
                $fail(__('The :attribute field must be a valid URL or local path.'));

                return;
            }

            if (preg_match('#(^|/)\.\.(/|$)#', $value) === 1) {
                $fail(__('The :attribute field must be a valid URL or local path.'));

                return;
            }

            return;
        }

        $scheme = parse_url($value, PHP_URL_SCHEME);

        if (
            filter_var($value, FILTER_VALIDATE_URL) !== false
            && in_array(strtolower((string) $scheme), ['http', 'https'], true)
        ) {
            return;
        }

        $fail(__('The :attribute field must be a valid URL or local path.'));
    }
}
