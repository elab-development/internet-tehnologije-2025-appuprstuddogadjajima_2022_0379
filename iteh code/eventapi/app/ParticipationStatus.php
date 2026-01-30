<?php

namespace App;

enum ParticipationStatus:string
{
    case REGISTERED = 'REGISTERED';
    case CANCELLED = 'CANCELLED';
    case ATTENDED = 'ATTENDED';
}
