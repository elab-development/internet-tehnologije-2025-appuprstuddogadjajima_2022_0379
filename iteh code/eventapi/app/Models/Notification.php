<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $fillable = [
        "idUser",
        "idEvent",
        "message",
        "type",
        "createdAt",
        "seen"
        
    ];
    
    
    protected $casts = [
         "type" => \App\Enums\NotificationType::class,
         "createdAt" => 'datetime',
            "seen" => 'boolean'

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
