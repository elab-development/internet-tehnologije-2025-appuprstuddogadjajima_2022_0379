<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventParticipation extends Model
{
 protected $fillable = [
        'status',
        'registeredAt',
        'cancelledAt',
        'attendanceMarkedAt',
    ];
    
    
    protected $casts = [
        'status' => \App\Enums\ParticipationStatus::class,
        'registeredAt' => 'datetime',
        'cancelledAt' => 'datetime',
        'attendanceMarkedAt' => 'datetime',
    ];
    }
