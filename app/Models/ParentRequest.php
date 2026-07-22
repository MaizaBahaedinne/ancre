<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ParentRequest extends Model
{
    use HasFactory;

    public const ACTION_DEMANDE = 'demande';
    public const ACTION_RECLAMATION = 'reclamation';

    public const ACTION_OPTIONS = [
        self::ACTION_DEMANDE => 'Demande',
        self::ACTION_RECLAMATION => 'Reclamation',
    ];

    public const STATUS_CREATED = 'cree';
    public const STATUS_ACKNOWLEDGED = 'accuse_reception';
    public const STATUS_IN_PROGRESS = 'en_cours_traitement';
    public const STATUS_PROCESSED = 'traite';
    public const STATUS_REJECTED = 'refuse';

    public const STATUS_OPTIONS = [
        self::STATUS_CREATED => 'Cree',
        self::STATUS_ACKNOWLEDGED => 'Accuse de reception',
        self::STATUS_IN_PROGRESS => 'En cours de traitement',
        self::STATUS_PROCESSED => 'Traite',
        self::STATUS_REJECTED => 'Refuse',
    ];

    protected $fillable = [
        'parent_id',
        'enfant_id',
        'action_type',
        'subject_id',
        'subject_snapshot',
        'subject_other',
        'description',
        'attachments',
        'workflow_status',
        'opened_at',
        'acknowledged_at',
        'in_progress_at',
        'resolved_at',
        'resolution_note',
        'handled_by_user_id',
    ];

    protected $casts = [
        'attachments' => 'array',
        'opened_at' => 'datetime',
        'acknowledged_at' => 'datetime',
        'in_progress_at' => 'datetime',
        'resolved_at' => 'datetime',
    ];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(ParentModel::class, 'parent_id');
    }

    public function enfant(): BelongsTo
    {
        return $this->belongsTo(Enfant::class);
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(ParentRequestSubject::class, 'subject_id');
    }

    public function handledBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'handled_by_user_id');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(ParentRequestMessage::class, 'parent_request_id');
    }

    public function subjectLabel(): string
    {
        return $this->subject_snapshot ?: ($this->subject_other ?: 'Autre');
    }

    public function workflowBadgeClass(): string
    {
        return match ($this->workflow_status) {
            self::STATUS_ACKNOWLEDGED => 'info',
            self::STATUS_IN_PROGRESS => 'primary',
            self::STATUS_PROCESSED => 'success',
            self::STATUS_REJECTED => 'danger',
            default => 'secondary',
        };
    }
}