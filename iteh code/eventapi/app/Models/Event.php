<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class Event extends Model
{
    //
    use HasFactory;
    protected $table = 'events';

     /** @use HasFactory<\Database\Factories\EventFactory> */
    protected $fillable = [

        "idUser",
        "idCategory",
        "title",
        "description",
        "location",
        "startAt",
        "endAt",
        "capacity",
        "status",
    ];

    protected $casts = [
    'startAt' => 'datetime',
    'endAt'   => 'datetime',
    'capacity'=> 'integer',
    'status'  => \App\EventStatus::class, // cast ka enum
];



    public function category()
    {
        return $this->belongsTo(Category::class, 'idCategory');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'idUser');
    }




    public function eventParticipations()
    {
        return $this->hasMany(EventParticipation::class, 'idEvent');
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class, 'idEvent');
    }

}
