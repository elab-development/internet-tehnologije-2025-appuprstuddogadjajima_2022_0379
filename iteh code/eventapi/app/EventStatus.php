<?php

namespace App;

enum EventStatus:string 
{
    case DRAFT = 'DRAFT';
    case ACTIVE = 'ACTIVE';
    case CANCELLED = 'CANCELLED';
    case FINISHED = 'FINISHED';
}
