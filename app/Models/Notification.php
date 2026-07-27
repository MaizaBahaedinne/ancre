<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'trigger',
        'subject',
        'description',
        'notification_type',
        'receiver_type',
        'receiver_id',
        'receiver_role',
        'metadata',
        'read_at',
    ];

    protected $casts = [
        'metadata' => 'json',
        'read_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function logs()
    {
        return $this->hasMany(NotificationLog::class);
    }

    public function isRead()
    {
        return $this->read_at !== null;
    }

    public function markAsRead()
    {
        $this->update(['read_at' => now()]);
    }
}
