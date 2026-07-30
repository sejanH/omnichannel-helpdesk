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
}
