<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'avatar',
        'status',
    ];

    public function assignedTickets()
    {
        return $this->hasMany(Ticket::class, 'assigned_agent_id');
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    /**
     * Get Cached User (Admin/Agent)
     */
    public static function getCachedUser(int $id): ?self
    {
        return \Illuminate\Support\Facades\Cache::remember("user_{$id}", 3600, function () use ($id) {
            return static::find($id);
        });
    }

    /**
     * Flush User Cache
     */
    public function flushUserCache(): void
    {
        \Illuminate\Support\Facades\Cache::forget("user_{$this->id}");
    }
}
