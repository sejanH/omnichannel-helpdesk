<?php

namespace App\Models;

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

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function assignedTickets()
    {
        return $this->hasMany(Ticket::class, 'assigned_agent_id');
    }

    public function invoices()
    {
        return $this->hasMany(BillingInvoice::class)->orderBy('created_at', 'desc');
    }

    /**
     * Dedicated Workspace Subscription Relation
     */
    public function subscription()
    {
        return $this->hasOne(Subscription::class, 'user_id');
    }

    /**
     * Get or Auto-Create Workspace Subscription
     */
    public function getOrProvisionSubscription(): Subscription
    {
        $sub = $this->subscription;
        if (!$sub) {
            $sub = Subscription::create([
                'user_id' => $this->id,
                'subscription_status' => 'trialing',
                'subscription_plan' => 'pro',
                'trial_ends_at' => now()->addDays(14),
                'max_agent_seats' => 10,
                'max_channels' => 10,
            ]);
        }
        return $sub;
    }

    /**
     * Convenience Proxy Helpers
     */
    public function hasActiveSubscription(): bool
    {
        return $this->getOrProvisionSubscription()->isActive();
    }

    public function onTrial(): bool
    {
        return $this->getOrProvisionSubscription()->onTrial();
    }

    public function daysLeftOnTrial(): int
    {
        return $this->getOrProvisionSubscription()->daysLeftOnTrial();
    }

    public function getPlanDetails(): array
    {
        return $this->getOrProvisionSubscription()->getPlanDetails();
    }

    /**
     * Get Cached User
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
