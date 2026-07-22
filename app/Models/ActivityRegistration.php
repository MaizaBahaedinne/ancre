<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityRegistration extends Model
{
    use HasFactory;

    public const PAYMENT_METHOD_CASH = 'Especes';
    public const PAYMENT_METHOD_CHEQUE = 'Cheque';
    public const PAYMENT_METHOD_CARD = 'Carte';
    public const PAYMENT_METHOD_ONLINE = 'En ligne';

    public const STATUS_PENDING_PAYMENT = 'en_attente_paiement';
    public const STATUS_VALIDATED = 'validee';
    public const STATUS_WAITLIST = 'liste_attente';
    public const STATUS_CANCELLED = 'annulee';

    public const PARTICIPATION_PRESENT = 'present';
    public const PARTICIPATION_ABSENT = 'absent';

    public const STATUS_OPTIONS = [
        self::STATUS_PENDING_PAYMENT => 'En attente de paiement',
        self::STATUS_VALIDATED => 'Validee',
        self::STATUS_WAITLIST => 'Liste d\'attente',
        self::STATUS_CANCELLED => 'Annulee',
    ];

    public const PARTICIPATION_OPTIONS = [
        self::PARTICIPATION_PRESENT => 'Present',
        self::PARTICIPATION_ABSENT => 'Absent',
    ];

    public const PAYMENT_METHOD_OPTIONS = [
        self::PAYMENT_METHOD_CASH => 'Especes',
        self::PAYMENT_METHOD_CHEQUE => 'Cheque',
        self::PAYMENT_METHOD_CARD => 'Carte',
        self::PAYMENT_METHOD_ONLINE => 'En ligne',
    ];

    protected $fillable = [
        'activite_id',
        'enfant_id',
        'parent_id',
        'status',
        'participation_status',
        'amount_due',
        'amount_paid',
        'paid_at',
        'payment_reference',
        'notes',
    ];

    protected $casts = [
        'amount_due' => 'decimal:2',
        'amount_paid' => 'decimal:2',
        'paid_at' => 'datetime',
    ];

    public function activite(): BelongsTo
    {
        return $this->belongsTo(Activite::class);
    }

    public function enfant(): BelongsTo
    {
        return $this->belongsTo(Enfant::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(ParentModel::class, 'parent_id');
    }

    public function statusBadgeClass(): string
    {
        return match ($this->status) {
            self::STATUS_VALIDATED => 'success',
            self::STATUS_WAITLIST => 'warning text-dark',
            self::STATUS_CANCELLED => 'secondary',
            default => 'info',
        };
    }
}
