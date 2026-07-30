<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'avatar',
        'external_ids',
        'notes',
    ];

    protected $casts = [
        'external_ids' => 'array',
    ];

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    /**
     * Get Cached Customer Contact
     */
    public static function getCachedContact(int $id): ?self
    {
        return \Illuminate\Support\Facades\Cache::remember("customer_contact_{$id}", 3600, function () use ($id) {
            return static::find($id);
        });
    }

    /**
     * Flush Customer Contact Cache
     */
    public function flushContactCache(): void
    {
        \Illuminate\Support\Facades\Cache::forget("customer_contact_{$this->id}");
    }
}
