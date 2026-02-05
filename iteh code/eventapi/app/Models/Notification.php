<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class Notification extends Model
{
    use HasFactory;
    protected $table = 'notifications';
    protected $fillable = [
        "idUser",
        "idEvent",
        "message",
        "type",
        "createdAt",
        "seen"
        
    ];
    
    
    protected $casts = [
         "type" => \App\NotificationType::class,
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
