<?php

namespace App\Policies;

use App\Models\ShortUrl;
use App\Models\User;

class ShortUrlPolicy
{
    /**
     * Determine if the user can view a specific short URL.
     */
    public function view(User $user, ShortUrl $shortUrl): bool
    {
        return $user->id === $shortUrl->user_id;
    }

    /**
     * Determine if the user can update the short URL.
     */
    public function update(User $user, ShortUrl $shortUrl): bool
    {
        return $user->id === $shortUrl->user_id;
    }

    /**
     * Determine if the user can delete the short URL.
     */
    public function delete(User $user, ShortUrl $shortUrl): bool
    {
        return $user->id === $shortUrl->user_id;
    }
}
