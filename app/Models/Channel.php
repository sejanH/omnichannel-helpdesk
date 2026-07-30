<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Channel extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'type',
        'icon',
        'configuration',
        'is_active',
    ];

    protected $casts = [
        'configuration' => 'array',
        'is_active' => 'boolean',
    ];

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    /**
     * Get Cached Channel Config from Database
     */
    public static function getCachedConfig(string $type): array
    {
        return \Illuminate\Support\Facades\Cache::remember("channel_config_{$type}", 86400, function () use ($type) {
            $channel = static::where('type', $type)->first();
            return $channel?->configuration ?? [];
        });
    }

    /**
     * Flush Cached Channel Config
     */
    public static function flushChannelCache(string $type): void
    {
        \Illuminate\Support\Facades\Cache::forget("channel_config_{$type}");
    }
}
