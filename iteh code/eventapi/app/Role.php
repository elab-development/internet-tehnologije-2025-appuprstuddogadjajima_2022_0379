<?php

namespace App;

enum Role:string
{
    case ADMIN = 'ADMIN';
    case ORGANIZATOR = 'ORGANIZATOR';
    case STUDENT = 'STUDENT';
}
