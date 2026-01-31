<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventParticipation extends Model
{
 protected $fillable = [
        "idEvent",
        "idUser",
        'status',
        'registeredAt',
        'cancelledAt',
        'attendanceMarkedAt'
    ];
    
    
    protected $casts = [
        'status' => \App\Enums\ParticipationStatus::class,
        'registeredAt' => 'datetime',
        'cancelledAt' => 'datetime',
        'attendanceMarkedAt' => 'datetime'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'idUser');
    }
    public function event()
    {
        return $this->belongsTo(Event::class, 'idEvent');   

    }
}