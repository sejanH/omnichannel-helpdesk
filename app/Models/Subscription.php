<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'stripe_id',
        'pm_type',
        'pm_last_four',
        'subscription_status',
        'subscription_plan',
        'trial_ends_at',
        'subscription_ends_at',
        'max_agent_seats',
        'max_channels',
    ];

    protected $casts = [
        'trial_ends_at' => 'datetime',
        'subscription_ends_at' => 'datetime',
    ];

    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Active Subscription Check
     */
    public function isActive(): bool
    {
        if (in_array($this->subscription_status, ['active', 'trialing'])) {
            return true;
        }

        if ($this->trial_ends_at && $this->trial_ends_at->isFuture()) {
            return true;
        }

        return false;
    }

    public function onTrial(): bool
    {
        return $this->subscription_status === 'trialing' || ($this->trial_ends_at && $this->trial_ends_at->isFuture());
    }

    public function daysLeftOnTrial(): int
    {
        if (!$this->trial_ends_at) return 0;
        return max(0, (int) now()->diffInDays($this->trial_ends_at, false));
    }

    public function getPlanDetails(): array
    {
        $plan = strtolower($this->subscription_plan ?? 'pro');
        return match ($plan) {
            'starter' => [
                'name' => 'Starter Plan',
                'price' => '$49/mo',
                'amount_cents' => 4900,
                'max_seats' => 3,
                'max_channels' => 2,
                'features' => ['Up to 3 Support Agents', '2 Live Chat Channels', 'Standard Response SLA', '30-Day Conversation Logs'],
            ],
            'enterprise' => [
                'name' => 'Enterprise Plan',
                'price' => '$299/mo',
                'amount_cents' => 29900,
                'max_seats' => 50,
                'max_channels' => 50,
                'features' => ['Up to 50 Support Agents', 'Unlimited Channels & Webhooks', 'Priority SLA Monitoring', 'Dedicated Account Manager'],
            ],
            default => [
                'name' => 'Pro Business Plan',
                'price' => '$149/mo',
                'amount_cents' => 14900,
                'max_seats' => 10,
                'max_channels' => 10,
                'features' => ['Up to 10 Support Agents', '10 Omnichannel Outlets', 'WhatsApp & Reverb WebSockets', 'Custom Widget Builder Studio'],
            ],
        };
    }
}
