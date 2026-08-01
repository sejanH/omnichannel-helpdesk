<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_number',
        'subject',
        'status',
        'priority',
        'channel_id',
        'contact_id',
        'assigned_agent_id',
        'last_activity_at',
        'first_responded_at',
        'resolved_at',
        'rating',
        'feedback_comment',
    ];

    protected $casts = [
        'last_activity_at' => 'datetime',
        'first_responded_at' => 'datetime',
        'resolved_at' => 'datetime',
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
}
