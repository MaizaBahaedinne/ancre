<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;
use Carbon\CarbonInterface;

class Incident extends Model
{
    use HasFactory;

    protected $fillable = [
        'enfant_id',
        'date',
        'type_incident',
        'workflow_status',
        'notify_parent',
        'opened_at',
        'responsable_personnel_id',
        'taken_at',
        'resolved_at',
        'closed_at',
        'description',
        'action_realisee',
        'attachments',
    ];

    protected $casts = [
        'date' => 'date',
        'notify_parent' => 'boolean',
        'opened_at' => 'datetime',
        'taken_at' => 'datetime',
        'resolved_at' => 'datetime',
        'closed_at' => 'datetime',
        'attachments' => 'array',
    ];

    public const WORKFLOW_OPEN = 'ouvert';
    public const WORKFLOW_TAKEN = 'pris_en_charge';
    public const WORKFLOW_IN_PROGRESS = 'en_cours';
    public const WORKFLOW_WAITING = 'en_attente';
    public const WORKFLOW_CLOSED = 'cloture';

    public const WORKFLOW_OPTIONS = [
        self::WORKFLOW_OPEN => 'Ouvert',
        self::WORKFLOW_TAKEN => 'Pris en charge',
        self::WORKFLOW_IN_PROGRESS => 'En cours',
        self::WORKFLOW_WAITING => 'En attente',
        self::WORKFLOW_CLOSED => 'Cloture',
    ];

    public function enfant(): BelongsTo
    {
        return $this->belongsTo(Enfant::class);
    }

    public function responsablePersonnel(): BelongsTo
    {
        return $this->belongsTo(Personnel::class, 'responsable_personnel_id');
    }

    public function getOpenToTakenMinutesAttribute(): ?int
    {
        if (! $this->opened_at || ! $this->taken_at) {
            return null;
        }

        return (int) $this->opened_at->diffInMinutes($this->taken_at);
    }

    public function getOpenToResolvedMinutesAttribute(): ?int
    {
        if (! $this->opened_at || ! $this->resolved_at) {
            return null;
        }

        return (int) $this->opened_at->diffInMinutes($this->resolved_at);
    }

    public function workflowBadgeClass(): string
    {
        return match ($this->workflow_status) {
            self::WORKFLOW_TAKEN => 'warning text-dark',
            self::WORKFLOW_IN_PROGRESS => 'primary',
            self::WORKFLOW_WAITING => 'secondary',
            self::WORKFLOW_CLOSED => 'success',
            default => 'danger',
        };
    }

    public function parentNotificationLabel(): string
    {
        return $this->notify_parent ? 'Oui' : 'Non';
    }
}
