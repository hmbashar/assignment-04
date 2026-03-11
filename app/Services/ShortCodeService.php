<?php

namespace App\Services;

use App\Models\ShortUrl;
use Illuminate\Support\Str;

class ShortCodeService
{
    /**
     * Generate a unique short code that does not already exist in the database.
     * Uses a random 6-character alphanumeric string. Retries on collision.
     *
     * @param  int  $length
     * @return string
     */
    public function generate(int $length = 6): string
    {
        do {
            $code = Str::random($length);
        } while (ShortUrl::where('short_code', $code)->exists());

        return $code;
    }
}
