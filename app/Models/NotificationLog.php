<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class NotificationLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'notification_id',
        'channel',
        'recipient',
        'status',
        'error_message',
        'response',
    ];

    protected $casts = [
        'response' => 'json',
    ];

    public function notification()
    {
        return $this->belongsTo(Notification::class);
    }
}
