<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ticket extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'ticket_number',
        'subject',
        'status',
        'priority',
        'tags',
        'category',
        'channel_id',
        'contact_id',
        'assigned_agent_id',
        'last_activity_at',
        'first_responded_at',
        'resolved_at',
        'due_at',
        'rating',
        'feedback_comment',
    ];

    protected $casts = [
        'tags' => 'array',
        'last_activity_at' => 'datetime',
        'first_responded_at' => 'datetime',
        'resolved_at' => 'datetime',
        'due_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function channel()
    {
        return $this->belongsTo(Channel::class);
    }

    public function contact()
    {
        return $this->belongsTo(Contact::class);
    }

    public function assignedAgent()
    {
        return $this->belongsTo(User::class, 'assigned_agent_id');
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    public function latestMessage()
    {
        return $this->hasOne(Message::class)->latestOfMany();
    }

    /**
     * Get SLA Status (overdue, due_soon, on_time, completed, none)
     */
    public function getSlaStatusAttribute(): string
    {
        if ($this->status === 'resolved' || $this->status === 'closed') {
            return 'completed';
        }

        if (!$this->due_at) {
            return 'none';
        }

        if (now()->isAfter($this->due_at)) {
            return 'overdue';
        }

        if (now()->diffInHours($this->due_at, false) <= 2) {
            return 'due_soon';
        }

        return 'on_time';
    }
}
