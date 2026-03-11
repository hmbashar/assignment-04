<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShortUrl extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'original_url',
        'short_code',
        'clicks',
        'expires_at',
    ];

    /**
     * PHP-level attribute defaults (mirrors DB defaults).
     *
     * @var array<string, mixed>
     */
    protected $attributes = [
        'clicks' => 0,
    ];

    /**
     * Cast attributes.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
            'clicks' => 'integer',
        ];
    }

    /**
     * Get the user that owns this URL.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
