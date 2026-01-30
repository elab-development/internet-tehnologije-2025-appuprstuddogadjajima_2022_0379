<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    //
    protected $fillable = [

        "idEvent",
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
    'status'  => \App\Enums\EventStatus::class, // cast ka enum
];
}
