<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class EventParticipation extends Model
{    protected $primaryKey = 'idParticipation';

    use HasFactory;
    protected $table = 'event_participations';
 protected $fillable = [
        "idEvent",
        "idUser",
        'status',
        'registeredAt',
        'cancelledAt',
        'attendanceMarkedAt'
    ];
    
    
    protected $casts = [
        'status' => \App\ParticipationStatus::class,
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