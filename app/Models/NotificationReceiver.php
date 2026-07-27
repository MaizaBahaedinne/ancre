<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class NotificationReceiver extends Model
{
    use HasFactory;

    protected $fillable = [
        'workflow_id',
        'receiver_type',
        'receiver_value',
        'notification_medium',
        'is_enabled',
        'conditions',
    ];

    protected $casts = [
        'conditions' => 'json',
        'is_enabled' => 'boolean',
    ];

    public function workflow()
    {
        return $this->belongsTo(NotificationWorkflow::class, 'workflow_id');
    }
}
