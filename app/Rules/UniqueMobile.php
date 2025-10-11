<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use App\Models\Household;

class UniqueMobile implements ValidationRule
{
    protected $table;
    protected $ignoreId;

    public function __construct(string $table = 'households', int $ignoreId = null)
    {
        $this->table = $table;
        $this->ignoreId = $ignoreId;
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (empty($value)) {
            return; // Allow null/empty values
        }

        // Normalize the input mobile number
        $normalizedMobile = preg_replace('/[^0-9+]/', '', $value);

        // Check if normalized mobile already exists
        $query = Household::where('mobile_normalized', $normalizedMobile);
        
        if ($this->ignoreId) {
            $query->where('id', '!=', $this->ignoreId);
        }

        if ($query->exists()) {
            $fail('The mobile number is already taken (formatting variations are considered duplicates).');
        }
    }
}
