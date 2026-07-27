<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class NotificationWorkflow extends Model
{
    use HasFactory;

    protected $fillable = [
        'trigger',
        'name',
        'description',
        'is_enabled',
        'module',
        'config',
    ];

    protected $casts = [
        'config' => 'json',
        'is_enabled' => 'boolean',
    ];

    public function receivers()
    {
        return $this->hasMany(NotificationReceiver::class, 'workflow_id');
    }

    public static function getByTrigger($trigger)
    {
        return self::where('trigger', $trigger)->where('is_enabled', true)->first();
    }
}
