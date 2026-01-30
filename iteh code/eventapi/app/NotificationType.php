<?php

namespace App;

enum NotificationType:string
{
    case REMINDER = 'REMINDER';
    case UPDATE = 'UPDATE';
    case CANCELLATION = 'CANCELLATION';
}
