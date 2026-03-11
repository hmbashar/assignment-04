<?php

namespace App\Http\Controllers;

use App\Models\ShortUrl;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;

class RedirectController extends Controller
{
    /**
     * GET /{short_code}
     *
     * - 302 Redirect if found and not expired.
     * - 410 Gone if the URL is expired.
     * - 404 Not Found if the code does not exist.
     */
    public function redirect(string $shortCode): RedirectResponse|JsonResponse
    {
        $shortUrl = ShortUrl::where('short_code', $shortCode)->first();

        if (!$shortUrl) {
            abort(404, 'Short URL not found.');
        }

        // Check if the URL has expired
        if ($shortUrl->expires_at !== null && $shortUrl->expires_at->isPast()) {
            return response()->json([
                'message' => 'This short URL has expired.',
            ], 410);
        }

        // Increment click counter
        $shortUrl->increment('clicks');

        return redirect($shortUrl->original_url, 302);
    }
}
