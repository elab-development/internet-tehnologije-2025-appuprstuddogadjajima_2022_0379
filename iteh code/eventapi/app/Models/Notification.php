<?php

namespace App\Models;

use App\NotificationType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $primaryKey = 'idNotification';

    public $incrementing = true;

    protected $keyType = 'int';

    protected $table = 'notifications';

    protected $fillable = [
        'idUser',
        'idEvent',
        'message',
        'type',
        'createdAt',
        'seen',

    ];

    protected $casts = [
        'type' => \App\NotificationType::class,
        'createdAt' => 'datetime',
        'seen' => 'boolean',

    ];

    public static function notifyUser(int $userId, int $eventId, string $message, NotificationType $type): self
    {
        return self::create([
            'idUser' => $userId,
            'idEvent' => $eventId,
            'message' => $message,
            'type' => $type,
            'seen' => false,
        ]);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'idUser');
    }

    public function event()
    {
        return $this->belongsTo(Event::class, 'idEvent');

    }
}
