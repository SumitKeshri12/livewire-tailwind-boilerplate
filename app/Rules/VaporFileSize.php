<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Storage;

class VaporFileSize implements ValidationRule
{
    protected $size;

    /**
     * Create a new rule instance.
     *
     * @param int $sizeInMb - default 2mb
     */
    public function __construct(int $sizeInMb = 0)
    {
        $this->size = $sizeInMb;
        if ($this->size == 0) {
            $this->size = config('constants.default_single_filesize');
        }
    }

    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (Storage::exists($value)) {
            $size = Storage::size($value);

            // https://www.gbmb.org/mb-to-bytes
            if (! ($size <= ($this->size * 1048576))) {
                $fail('The :attribute size must be a less then or equal to ' . config('constants.default_single_filesize') . ' MB.');
            }     // 1048576 byte = 1MB
        }
    }
}
