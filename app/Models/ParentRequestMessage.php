<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ParentRequestMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'parent_request_id',
        'sender_user_id',
        'message',
        'attachments',
    ];

    protected $casts = [
        'attachments' => 'array',
    ];

    public function request(): BelongsTo
    {
        return $this->belongsTo(ParentRequest::class, 'parent_request_id');
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_user_id');
    }
}