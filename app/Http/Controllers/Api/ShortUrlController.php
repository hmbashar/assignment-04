<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Url\StoreUrlRequest;
use App\Http\Requests\Url\UpdateUrlRequest;
use App\Models\ShortUrl;
use App\Services\ShortCodeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ShortUrlController extends Controller
{

    public function __construct(private readonly ShortCodeService $shortCodeService)
    {
    }

    /**
     * GET /api/urls
     * Return a paginated list of the authenticated user's URLs.
     */
    public function index(Request $request): JsonResponse
    {
        $urls = $request->user()
            ->shortUrls()
            ->latest()
            ->paginate(10);

        return response()->json($urls, 200);
    }

    /**
     * POST /api/urls
     * Create a new shortened URL for the authenticated user.
     */
    public function store(StoreUrlRequest $request): JsonResponse
    {
        $shortUrl = $request->user()->shortUrls()->create([
            'original_url' => $request->original_url,
            'short_code' => $this->shortCodeService->generate(),
            'expires_at' => $request->expires_at,
        ]);

        return response()->json([
            'message' => 'Short URL created successfully.',
            'short_url' => $this->formatUrl($shortUrl),
        ], 201);
    }

    /**
     * GET /api/urls/{id}
     * Return a specific URL owned by the authenticated user.
     */
    public function show(Request $request, ShortUrl $url): JsonResponse
    {
        if ($request->user()->cannot('view', $url)) {
            return response()->json(['message' => 'This action is unauthorized.'], 403);
        }

        return response()->json([
            'short_url' => $this->formatUrl($url),
        ], 200);
    }

    /**
     * PUT /api/urls/{id}
     * Update original_url or expires_at for a URL owned by the authenticated user.
     */
    public function update(UpdateUrlRequest $request, ShortUrl $url): JsonResponse
    {
        if ($request->user()->cannot('update', $url)) {
            return response()->json(['message' => 'This action is unauthorized.'], 403);
        }

        $url->update($request->validated());

        return response()->json([
            'message' => 'Short URL updated successfully.',
            'short_url' => $this->formatUrl($url->fresh()),
        ], 200);
    }

    /**
     * DELETE /api/urls/{id}
     * Delete a URL owned by the authenticated user.
     */
    public function destroy(Request $request, ShortUrl $url): JsonResponse
    {
        if ($request->user()->cannot('delete', $url)) {
            return response()->json(['message' => 'This action is unauthorized.'], 403);
        }

        $url->delete();

        return response()->json(null, 204);
    }

    /**
     * Format a ShortUrl model into the JSON response shape, including the public redirect link.
     */
    private function formatUrl(ShortUrl $url): array
    {
        return [
            'id' => $url->id,
            'original_url' => $url->original_url,
            'short_code' => $url->short_code,
            'short_link' => url($url->short_code),
            'clicks' => $url->clicks,
            'expires_at' => $url->expires_at?->toIso8601String(),
            'created_at' => $url->created_at->toIso8601String(),
            'updated_at' => $url->updated_at->toIso8601String(),
        ];
    }
}
