@extends('adminlte::page')

@section('title', 'Detail Inscription')

@section('content_header')
<h1 class="m-0">Detail inscription</h1>
@stop

@section('css')
<style>
.inscription-shell {
    display: grid;
    gap: 1.5rem;
}

.inscription-hero {
    border: 0;
    border-radius: 1.5rem;
    overflow: hidden;
    background: linear-gradient(135deg, #fff8ee 0%, #ffffff 52%, #eef7ff 100%);
    box-shadow: 0 18px 40px rgba(15, 23, 42, 0.08);
}

.inscription-hero .card-body {
    padding: 1.75rem;
}

.inscription-hero-grid {
    display: grid;
    grid-template-columns: 140px minmax(0, 1fr) auto;
    gap: 1.5rem;
    align-items: center;
}

.inscription-avatar {
    width: 140px;
    height: 140px;
    border-radius: 1.25rem;
    object-fit: cover;
    box-shadow: 0 10px 24px rgba(15, 23, 42, 0.16);
}

.inscription-avatar-placeholder {
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 3rem;
    font-weight: 700;
    color: #0f172a;
    background: linear-gradient(135deg, #ffd8a8 0%, #fff0d6 100%);
}

.inscription-kicker {
    margin: 0 0 0.35rem;
    color: #b45309;
    font-size: 0.78rem;
    letter-spacing: 0.18em;
    text-transform: uppercase;
    font-weight: 700;
}

.inscription-title {
    margin: 0;
    font-size: clamp(1.8rem, 3vw, 2.5rem);
    font-weight: 800;
    color: #0f172a;
}

.inscription-meta,
.inscription-chips {
    display: flex;
    flex-wrap: wrap;
    gap: 0.75rem;
    margin-top: 1rem;
}

.inscription-meta span,
.inscription-chip {
    display: inline-flex;
    align-items: center;
    gap: 0.45rem;
    border-radius: 999px;
    padding: 0.55rem 0.9rem;
}

.inscription-meta span {
    background: rgba(255, 255, 255, 0.7);
    color: #334155;
}

.inscription-chip {
    background: #fff;
    border: 1px solid rgba(148, 163, 184, 0.24);
    color: #0f172a;
    font-weight: 600;
}

.inscription-chip.is-current {
    background: #dcfce7;
    border-color: #86efac;
    color: #166534;
}

.inscription-chip.is-archive {
    background: #e2e8f0;
    border-color: #cbd5e1;
    color: #334155;
}

.inscription-actions {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.inscription-stat-card {
    height: 100%;
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1.15rem 1.25rem;
    border-radius: 1.25rem;
    background: #fff;
    box-shadow: 0 12px 28px rgba(15, 23, 42, 0.06);
}

.inscription-stat-icon {
    width: 3rem;
    height: 3rem;
    border-radius: 1rem;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    font-size: 1.1rem;
}

.inscription-stat-card small,
.inscription-side-list span,
.inscription-payment-subline {
    display: block;
    color: #64748b;
}

.inscription-stat-card strong {
    display: block;
    font-size: 1.2rem;
    color: #0f172a;
}

.inscription-side-card,
.inscription-panel-card {
    border: 0;
    border-radius: 1.25rem;
    box-shadow: 0 12px 28px rgba(15, 23, 42, 0.06);
}

.inscription-side-list {
    list-style: none;
    padding: 0;
    margin: 0;
    display: grid;
    gap: 1rem;
}

.inscription-side-list li {
    display: grid;
    grid-template-columns: 1.75rem minmax(0, 1fr) auto;
    gap: 0.75rem;
    align-items: center;
}

.inscription-side-list i {
    color: #ea580c;
}

.inscription-side-list strong {
    color: #0f172a;
}

.inscription-note {
    margin-top: 1rem;
    padding: 1rem;
    border-radius: 1rem;
    background: #f8fafc;
    color: #334155;
}

.inscription-payment-row {
    display: grid;
    grid-template-columns: minmax(0, 1.2fr) minmax(0, 1fr) minmax(0, 1fr) auto;
    gap: 1rem;
    padding: 1rem 0;
    border-bottom: 1px solid #e2e8f0;
}

.inscription-payment-row:last-child {
    border-bottom: 0;
    padding-bottom: 0;
}

.inscription-payment-month {
    font-weight: 700;
    color: #0f172a;
}

.inscription-money {
    font-weight: 700;
    color: #0f172a;
}

.inscription-badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 999px;
    padding: 0.4rem 0.8rem;
    font-size: 0.82rem;
    font-weight: 700;
}

.inscription-badge.is-success {
    background: #dcfce7;
    color: #166534;
}

.inscription-badge.is-warning {
    background: #fef3c7;
    color: #92400e;
}

.inscription-badge.is-danger {
    background: #fee2e2;
    color: #b91c1c;
}

.inscription-badge.is-muted {
    background: #e2e8f0;
    color: #334155;
}

.inscription-payment-actions {
    display: flex;
    flex-wrap: wrap;
    justify-content: flex-end;
    gap: 0.5rem;
    margin-top: 0.75rem;
}

.inscription-modal-body {
    padding: 1.25rem;
}

.inscription-modal-title {
    margin: 0;
    font-size: 1.1rem;
    font-weight: 800;
    color: #0f172a;
}

.inscription-modal-subtitle {
    margin: 0.35rem 0 0;
    color: #64748b;
}

@media (max-width: 991.98px) {
    .inscription-hero-grid {
        grid-template-columns: 1fr;
    }

    .inscription-actions {
        flex-direction: row;
        flex-wrap: wrap;
    }

    .inscription-payment-row {
        grid-template-columns: 1fr;
        gap: 0.65rem;
    }
}
</style>
@stop

@section('content')
@php
    $displayName = trim(($inscription->enfant?->prenom ?? '').' '.($inscription->enfant?->nom ?? ''));
    $parentName = trim(($inscription->enfant?->parent?->prenom ?? '').' '.($inscription->enfant?->parent?->nom ?? ''));
    $initial = strtoupper(substr($inscription->enfant?->prenom ?: $inscription->enfant?->nom ?: 'E', 0, 1));
    $isCurrentYear = ! $activeAcademicYearLabel || $inscription->annee_scolaire === $activeAcademicYearLabel;
    $currentOutstanding = max($expectedAmountTotal - $paidAmountTotal, 0);
    $statusBadgeClass = match ($inscription->statut) {
        'Active', 'Renouvelee' => 'is-success',
        'Suspendue' => 'is-warning',
        'Annulee' => 'is-danger',
        default => 'is-muted',
    };
    $paymentBadgeClass = static function (string $status): string {
        return match ($status) {
            'Paye' => 'is-success',
            'Partiel' => 'is-warning',
            'En retard' => 'is-danger',
            default => 'is-muted',
        };
    };
    $quickPaymentFallbackMonthLabel = old('month_label');

    if (! $quickPaymentFallbackMonthLabel && old('mois') && old('annee')) {
        $quickPaymentFallbackMonthLabel = sprintf('%02d/%04d', (int) old('mois'), (int) old('annee'));
    }

    $quickPaymentFallbackMonthLabel = $quickPaymentFallbackMonthLabel ?: 'le mois selectionne';
@endphp

<section class="inscription-shell">
    @if(session('success'))
        <div class="alert alert-success mb-0">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger mb-0">{{ $errors->first() }}</div>
    @endif

    <div class="inscription-hero card">
        <div class="card-body">
            <div class="inscription-hero-grid">
                <div>
                    @if($inscription->enfant?->photo)
                        <img src="{{ asset('storage/' . $inscription->enfant->photo) }}" alt="Photo de {{ $displayName }}" class="inscription-avatar">
                    @else
                        <div class="inscription-avatar inscription-avatar-placeholder">{{ $initial }}</div>
                    @endif
                </div>

                <div>
                    <p class="inscription-kicker">Dossier d'inscription</p>
                    <h2 class="inscription-title">{{ $displayName ?: 'Enfant non renseigne' }}</h2>
                    <div class="inscription-meta">
                        <span><i class="fa-solid fa-user-group"></i>{{ $parentName ?: 'Parent non renseigne' }}</span>
                        <span><i class="fa-solid fa-box-open"></i>{{ $inscription->package?->nom ?: 'Sans package' }}</span>
                        <span><i class="fa-solid fa-calendar-days"></i>{{ optional($inscription->date_inscription)->format('d/m/Y') ?: '-' }}</span>
                    </div>
                    <div class="inscription-chips">
                        <span class="inscription-chip {{ $isCurrentYear ? 'is-current' : 'is-archive' }}">
                            <i class="fa-solid {{ $isCurrentYear ? 'fa-bolt' : 'fa-box-archive' }}"></i>
                            {{ $isCurrentYear ? 'Annee en cours' : 'Archive' }}
                        </span>
                        <span class="inscription-chip"><i class="fa-solid fa-graduation-cap"></i>{{ $inscription->annee_scolaire }}</span>
                        <span class="inscription-chip"><i class="fa-solid fa-clock"></i>{{ $inscription->type_garde }}</span>
                        <span class="inscription-chip"><i class="fa-solid fa-receipt"></i>{{ $trackedMonthsCount }} mois suivis</span>
                    </div>
                </div>

                <div class="inscription-actions">
                    <span class="inscription-badge {{ $statusBadgeClass }}">{{ $inscription->statut }}</span>
                    <a href="{{ route('inscriptions.edit', $inscription) }}" class="btn btn-warning">Modifier</a>
                    @if($inscription->enfant)
                        <a href="{{ route('enfants.show', $inscription->enfant) }}" class="btn btn-outline-primary">Voir fiche enfant</a>
                    @endif
                    <a href="{{ route('inscriptions.index') }}" class="btn btn-secondary">Retour</a>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-xl-3 col-md-6">
            <div class="inscription-stat-card">
                <div class="inscription-stat-icon bg-primary"><i class="fa-solid fa-wallet"></i></div>
                <div>
                    <small>Mensualite package</small>
                    <strong>{{ number_format((float) $inscription->resolved_package_monthly_total, 2, ',', ' ') }} TND</strong>
                    <span>Montant attendu chaque mois</span>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="inscription-stat-card">
                <div class="inscription-stat-icon bg-info"><i class="fa-solid fa-file-invoice-dollar"></i></div>
                <div>
                    <small>Frais annuels</small>
                    <strong>{{ number_format((float) $inscription->resolved_annual_registration_fee, 2, ',', ' ') }} TND</strong>
                    <span>Ajoutes au premier mois</span>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="inscription-stat-card">
                <div class="inscription-stat-icon bg-success"><i class="fa-solid fa-circle-check"></i></div>
                <div>
                    <small>Mois regles</small>
                    <strong>{{ $paidMonthsCount }}</strong>
                    <span>Sur {{ $trackedMonthsCount }} mois suivis</span>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="inscription-stat-card">
                <div class="inscription-stat-icon bg-danger"><i class="fa-solid fa-triangle-exclamation"></i></div>
                <div>
                    <small>Reste a regler</small>
                    <strong>{{ number_format((float) $currentOutstanding, 2, ',', ' ') }} TND</strong>
                    <span>{{ $lateMonthsCount }} mois en attente ou en retard</span>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-xl-4">
            <div class="card inscription-side-card h-100">
                <div class="card-header border-0 pb-0">
                    <h3 class="card-title">Synthese inscription</h3>
                </div>
                <div class="card-body">
                    <ul class="inscription-side-list">
                        <li><i class="fa-solid fa-user"></i><span>Enfant</span><strong>{{ $displayName ?: '-' }}</strong></li>
                        <li><i class="fa-solid fa-user-group"></i><span>Parent</span><strong>{{ $parentName ?: '-' }}</strong></li>
                        <li><i class="fa-solid fa-box-open"></i><span>Package</span><strong>{{ $inscription->package?->nom ?: '-' }}</strong></li>
                        <li><i class="fa-solid fa-school"></i><span>Annee scolaire</span><strong>{{ $inscription->annee_scolaire }}</strong></li>
                        <li><i class="fa-solid fa-calendar-plus"></i><span>Date d'inscription</span><strong>{{ optional($inscription->date_inscription)->format('d/m/Y') ?: '-' }}</strong></li>
                        <li><i class="fa-solid fa-clock"></i><span>Type de garde</span><strong>{{ $inscription->type_garde }}</strong></li>
                        <li><i class="fa-solid fa-money-bill-wave"></i><span>Total premiere inscription</span><strong>{{ number_format((float) $inscription->resolved_total_amount, 2, ',', ' ') }} TND</strong></li>
                    </ul>

                    <div class="inscription-note">
                        <strong>Lecture de l'historique</strong>
                        <p class="mb-0 mt-2">Le suivi ci-dessous reconstruit chaque mensualite attendue depuis la date d'inscription et la compare aux paiements reels enregistres pour cet enfant.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-8">
            <div class="card inscription-panel-card">
                <div class="card-header border-0">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <div>
                            <h3 class="card-title mb-1">Historique mensuel des paiements</h3>
                            <small class="text-muted">Depuis {{ optional($inscription->date_inscription)->format('d/m/Y') ?: '-' }}</small>
                        </div>
                        <div class="text-end">
                            <strong>{{ number_format((float) $paidAmountTotal, 2, ',', ' ') }} TND</strong>
                            <small class="d-block text-muted">regles sur {{ number_format((float) $expectedAmountTotal, 2, ',', ' ') }} TND attendus</small>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @forelse($monthlyPaymentHistory as $historyItem)
                        @php
                            $latestPayment = $historyItem['latest_payment'];
                            $badgeClass = $paymentBadgeClass($historyItem['status']);
                        @endphp
                        <article class="inscription-payment-row">
                            <div>
                                <div class="inscription-payment-month">{{ $historyItem['month_label'] }}</div>
                                <div class="inscription-payment-subline">
                                    Mensualite: {{ number_format((float) $historyItem['expected_monthly_total'], 2, ',', ' ') }} TND
                                    @if($historyItem['expected_registration_fee'] > 0)
                                        | Frais annuels: {{ number_format((float) $historyItem['expected_registration_fee'], 2, ',', ' ') }} TND
                                    @endif
                                </div>
                                @if($latestPayment)
                                    <div class="inscription-payment-subline mt-1">
                                        Paiement du {{ optional($latestPayment->date_paiement)->format('d/m/Y') ?: '-' }}
                                        @if($latestPayment->mode_paiement)
                                            | {{ $latestPayment->mode_paiement }}
                                        @endif
                                        @if($historyItem['payments']->count() > 1)
                                            | {{ $historyItem['payments']->count() }} enregistrements
                                        @endif
                                    </div>
                                @else
                                    <div class="inscription-payment-subline mt-1">Aucun paiement enregistre pour ce mois.</div>
                                @endif
                            </div>

                            <div>
                                <small class="inscription-payment-subline">Attendu</small>
                                <div class="inscription-money">{{ number_format((float) $historyItem['expected_total'], 2, ',', ' ') }} TND</div>
                            </div>

                            <div>
                                <small class="inscription-payment-subline">Regle / Reste</small>
                                <div class="inscription-money">{{ number_format((float) $historyItem['paid_amount'], 2, ',', ' ') }} TND</div>
                                <small class="inscription-payment-subline">Reste: {{ number_format((float) $historyItem['balance'], 2, ',', ' ') }} TND</small>
                            </div>

                            <div class="text-md-end">
                                <span class="inscription-badge {{ $badgeClass }}">{{ $historyItem['status'] }}</span>
                                <div class="inscription-payment-actions">
                                    @if($historyItem['balance'] > 0 && $inscription->enfant_id)
                                        @can('payments.create')
                                            <button
                                                type="button"
                                                class="btn btn-primary btn-sm js-open-quick-payment"
                                                data-month-label="{{ $historyItem['month_label'] }}"
                                                data-month="{{ (int) substr($historyItem['month_key'], 5, 2) }}"
                                                data-year="{{ (int) substr($historyItem['month_key'], 0, 4) }}"
                                                data-balance="{{ number_format((float) $historyItem['balance'], 2, '.', '') }}"
                                                data-expected="{{ number_format((float) $historyItem['expected_total'], 2, '.', '') }}"
                                                data-paid="{{ number_format((float) $historyItem['paid_amount'], 2, '.', '') }}"
                                                data-child="{{ $displayName }}"
                                            >Ajouter paiement</button>
                                        @endcan
                                    @endif
                                    @if($latestPayment)
                                        <a href="{{ route('paiements.show', $latestPayment) }}" class="btn btn-outline-secondary btn-sm">Voir paiement</a>
                                    @endif
                                </div>
                            </div>
                        </article>
                    @empty
                        <p class="text-muted m-0">Aucun historique de paiement disponible pour cette inscription.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <div class="card inscription-panel-card">
        <div class="card-header border-0 d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h3 class="card-title mb-0">Suivi scolaire et evolution</h3>
            <div class="d-flex gap-2 flex-wrap">
                @foreach($trimesterStatuses as $trimesterLabel => $isFilled)
                    <span class="inscription-badge {{ $isFilled ? 'is-success' : 'is-muted' }}">
                        {{ $trimesterLabel }}: {{ $isFilled ? 'Renseigne' : 'En attente' }}
                    </span>
                @endforeach
            </div>
        </div>
        <div class="card-body">
            @if($errors->has('evaluations'))
                <div class="alert alert-danger">{{ $errors->first('evaluations') }}</div>
            @endif

            <div class="alert alert-light border">
                <strong>Niveau de l'enfant:</strong> {{ $currentLevel ?: 'Non defini' }}
                <br>
                <strong>Annee scolaire de l'inscription:</strong> {{ $inscription->annee_scolaire }}
                <br>
                <strong>Matieres trouvees:</strong> {{ $subjectCatalog->count() }}
            </div>

            @if(! $evaluationAcademicYear)
                <div class="alert alert-warning mb-0">L'annee scolaire de cette inscription n'existe pas dans le module des annees scolaires.</div>
            @elseif($subjectCatalog->isEmpty())
                <div class="alert alert-warning mb-0">Aucune matiere active n'est configuree pour ce niveau.</div>
            @else
                <div class="table-responsive mb-3">
                    <table class="table table-bordered table-sm mb-0">
                        <thead>
                        <tr>
                            <th>Trimestre</th>
                            <th>Moyenne generale</th>
                            <th>Rang</th>
                            <th>Date bulletin</th>
                            <th>Etat</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach(\App\Models\EnfantEvaluation::TRIMESTER_OPTIONS as $trimesterLabel)
                            @php
                                $trimesterEvaluation = $activeYearEvaluations->get($trimesterLabel);
                            @endphp
                            <tr>
                                <td>{{ $trimesterLabel }}</td>
                                <td>{{ $trimesterEvaluation?->general_average !== null ? number_format((float) $trimesterEvaluation->general_average, 2, ',', ' ') : '-' }}</td>
                                <td>{{ $trimesterEvaluation?->class_rank ?: '-' }}</td>
                                <td>{{ optional($trimesterEvaluation?->bulletin_received_at)->format('d/m/Y') ?: '-' }}</td>
                                <td>
                                    <span class="inscription-badge {{ $trimesterEvaluation ? 'is-success' : 'is-muted' }}">
                                        {{ $trimesterEvaluation ? 'Renseigne' : 'En attente' }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>

                @can('registrations.update')
                    <div class="accordion" id="accordionInscriptionTracking">
                        @foreach(\App\Models\EnfantEvaluation::TRIMESTER_OPTIONS as $trimesterLabel)
                            @php
                                $trimesterEvaluation = $activeYearEvaluations->get($trimesterLabel);
                                $gradeMap = $trimesterEvaluation
                                    ? $trimesterEvaluation->grades->pluck('grade', 'academic_subject_id')
                                    : collect();
                                $collapseId = 'inscriptionTrimesterForm'.\Illuminate\Support\Str::slug($trimesterLabel);
                                $headingId = 'inscriptionHeading'.\Illuminate\Support\Str::slug($trimesterLabel);
                            @endphp
                            <div class="card">
                                <div class="card-header" id="{{ $headingId }}">
                                    <h2 class="mb-0">
                                        <button class="btn btn-link btn-block text-left" type="button" data-toggle="collapse" data-target="#{{ $collapseId }}" aria-expanded="false" aria-controls="{{ $collapseId }}">
                                            {{ $trimesterLabel }}
                                            @if($trimesterEvaluation)
                                                <span class="badge badge-success ml-2">Mise a jour</span>
                                            @endif
                                        </button>
                                    </h2>
                                </div>

                                <div id="{{ $collapseId }}" class="collapse" aria-labelledby="{{ $headingId }}" data-parent="#accordionInscriptionTracking">
                                    <div class="card-body">
                                        <form method="POST" action="{{ route('inscriptions.evaluations.upsert', $inscription) }}">
                                            @csrf
                                            <input type="hidden" name="trimester" value="{{ $trimesterLabel }}">

                                            <div class="row g-3">
                                                <div class="col-md-4 form-group">
                                                    <label>Moyenne generale</label>
                                                    <input type="number" step="0.01" min="0" max="20" name="general_average" class="form-control @error('general_average') is-invalid @enderror" value="{{ old('trimester') === $trimesterLabel ? old('general_average') : $trimesterEvaluation?->general_average }}" placeholder="Auto si vide">
                                                    @error('general_average') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                                </div>
                                                <div class="col-md-4 form-group">
                                                    <label>Rang dans la classe</label>
                                                    <input type="number" min="1" max="200" name="class_rank" class="form-control @error('class_rank') is-invalid @enderror" value="{{ old('trimester') === $trimesterLabel ? old('class_rank') : $trimesterEvaluation?->class_rank }}">
                                                    @error('class_rank') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                                </div>
                                                <div class="col-md-4 form-group">
                                                    <label>Date reception bulletin</label>
                                                    <input type="date" name="bulletin_received_at" class="form-control @error('bulletin_received_at') is-invalid @enderror" value="{{ old('trimester') === $trimesterLabel ? old('bulletin_received_at') : optional($trimesterEvaluation?->bulletin_received_at)->format('Y-m-d') }}">
                                                    @error('bulletin_received_at') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                                </div>
                                            </div>

                                            <div class="table-responsive mt-2">
                                                <table class="table table-striped table-bordered table-sm mb-0">
                                                    <thead>
                                                    <tr>
                                                        <th>Matiere</th>
                                                        <th>Coefficient</th>
                                                        <th>Note /20</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    @forelse($subjectCatalog as $subject)
                                                        @php
                                                            $gradeField = 'grades.'.$subject->id;
                                                            $gradeValue = old('trimester') === $trimesterLabel
                                                                ? old($gradeField)
                                                                : $gradeMap->get($subject->id);
                                                        @endphp
                                                        <tr>
                                                            <td>{{ $subject->name }}</td>
                                                            <td>{{ number_format((float) $subject->default_coefficient, 2, ',', ' ') }}</td>
                                                            <td>
                                                                <input type="number" step="0.01" min="0" max="20" name="grades[{{ $subject->id }}]" class="form-control form-control-sm @error($gradeField) is-invalid @enderror" value="{{ $gradeValue }}">
                                                                @error($gradeField) <div class="invalid-feedback">{{ $message }}</div> @enderror
                                                            </td>
                                                        </tr>
                                                    @empty
                                                        <tr>
                                                            <td colspan="3" class="text-center text-muted">Aucune matiere active detectee pour ce niveau.</td>
                                                        </tr>
                                                    @endforelse
                                                    </tbody>
                                                </table>
                                            </div>

                                            <div class="mt-3 form-group">
                                                <label>Commentaire</label>
                                                <textarea name="notes" rows="3" class="form-control @error('notes') is-invalid @enderror" placeholder="Observations du bulletin">{{ old('trimester') === $trimesterLabel ? old('notes') : $trimesterEvaluation?->notes }}</textarea>
                                                @error('notes') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                            </div>

                                            <button type="submit" class="btn btn-primary">Enregistrer {{ $trimesterLabel }}</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endcan
            @endif
        </div>
    </div>
</section>

@can('payments.create')
<x-modal name="inscription-quick-payment-modal" maxWidth="lg" focusable>
    <div class="inscription-modal-body">
        <div class="d-flex justify-content-between align-items-start gap-3 mb-4">
            <div>
                <h3 class="inscription-modal-title">Ajouter un paiement</h3>
                <p class="inscription-modal-subtitle" id="quick-payment-context">Paiement mensuel sur cette inscription.</p>
            </div>
            <button type="button" class="btn btn-outline-secondary btn-sm" onclick="window.dispatchEvent(new CustomEvent('close-modal', { detail: 'inscription-quick-payment-modal' }))">Fermer</button>
        </div>

        <form method="POST" action="{{ route('inscriptions.payments.store', $inscription) }}" class="row g-3" id="quick-payment-form">
            @csrf
            <input type="hidden" name="quick_payment_modal" value="1">
            <div class="col-md-6">
                <label class="form-label">Enfant</label>
                <input type="text" class="form-control" value="{{ $displayName ?: '-' }}" readonly>
            </div>
            <div class="col-md-3">
                <label class="form-label">Mois</label>
                <input type="number" id="quick-payment-month" name="mois" class="form-control @error('mois') is-invalid @enderror" value="{{ old('mois') }}" min="1" max="12" readonly required>
                @error('mois') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-3">
                <label class="form-label">Annee</label>
                <input type="number" id="quick-payment-year" name="annee" class="form-control @error('annee') is-invalid @enderror" value="{{ old('annee') }}" min="2000" max="2100" readonly required>
                @error('annee') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-6">
                <label class="form-label">Montant (TND)</label>
                <input type="number" step="0.01" min="0.01" id="quick-payment-amount" name="montant" class="form-control @error('montant') is-invalid @enderror" value="{{ old('montant') }}" required>
                @error('montant') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-6">
                <label class="form-label">Date paiement</label>
                <input type="date" name="date_paiement" class="form-control @error('date_paiement') is-invalid @enderror" value="{{ old('date_paiement', now()->format('Y-m-d')) }}" required>
                @error('date_paiement') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-6">
                <label class="form-label">Mode paiement</label>
                <select name="mode_paiement" class="form-control @error('mode_paiement') is-invalid @enderror" required>
                    @foreach(['Especes','Carte','Virement','Cheque'] as $mode)
                        <option value="{{ $mode }}" @selected(old('mode_paiement', 'Especes') === $mode)>{{ $mode }}</option>
                    @endforeach
                </select>
                @error('mode_paiement') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-6">
                <label class="form-label">Reste maximal</label>
                <input type="text" id="quick-payment-balance-display" class="form-control" value="-" readonly>
            </div>
            <div class="col-12">
                <label class="form-label">Commentaire</label>
                <textarea name="commentaire" rows="2" class="form-control @error('commentaire') is-invalid @enderror">{{ old('commentaire') }}</textarea>
                @error('commentaire') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-12 d-flex justify-content-end gap-2">
                <button type="button" class="btn btn-outline-secondary" onclick="window.dispatchEvent(new CustomEvent('close-modal', { detail: 'inscription-quick-payment-modal' }))">Annuler</button>
                <button type="submit" class="btn btn-primary">Enregistrer le paiement</button>
            </div>
        </form>
    </div>
</x-modal>
@endcan
@stop

@section('js')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const openButtons = document.querySelectorAll('.js-open-quick-payment');
    const monthInput = document.getElementById('quick-payment-month');
    const yearInput = document.getElementById('quick-payment-year');
    const amountInput = document.getElementById('quick-payment-amount');
    const balanceDisplay = document.getElementById('quick-payment-balance-display');
    const contextLabel = document.getElementById('quick-payment-context');

    if (monthInput && yearInput && amountInput && balanceDisplay && contextLabel) {
        const formatMoney = (value) => {
            const number = Number(value || 0);
            return number.toLocaleString('fr-FR', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' TND';
        };

        const openQuickPaymentModal = (payload) => {
            monthInput.value = payload.month || '';
            yearInput.value = payload.year || '';
            amountInput.max = payload.balance || '';

            if (!amountInput.value || Number(amountInput.value) > Number(payload.balance || 0)) {
                amountInput.value = payload.balance || '';
            }

            balanceDisplay.value = formatMoney(payload.balance);
            contextLabel.textContent = 'Paiement pour ' + payload.monthLabel + ' - reste a regler: ' + formatMoney(payload.balance);

            window.dispatchEvent(new CustomEvent('open-modal', { detail: 'inscription-quick-payment-modal' }));
        };

        openButtons.forEach((button) => {
            button.addEventListener('click', function () {
                openQuickPaymentModal({
                    month: this.dataset.month,
                    year: this.dataset.year,
                    balance: this.dataset.balance,
                    monthLabel: this.dataset.monthLabel,
                });
            });
        });

        @if(old('quick_payment_modal'))
        openQuickPaymentModal({
            month: @json(old('mois')),
            year: @json(old('annee')),
            balance: @json(old('remaining_balance', old('montant'))),
            monthLabel: @json($quickPaymentFallbackMonthLabel),
        });
        @endif
    }

    const accordion = document.getElementById('accordionInscriptionTracking');

    if (accordion) {
        const triggers = Array.from(accordion.querySelectorAll('[data-toggle="collapse"][data-target]'));

        const closeAll = function () {
            triggers.forEach(function (trigger) {
                const targetSelector = trigger.getAttribute('data-target');
                const pane = targetSelector ? accordion.querySelector(targetSelector) : null;

                trigger.setAttribute('aria-expanded', 'false');

                if (pane) {
                    pane.classList.remove('show');
                }
            });
        };

        triggers.forEach(function (trigger) {
            trigger.addEventListener('click', function (event) {
                event.preventDefault();

                const targetSelector = trigger.getAttribute('data-target');

                if (!targetSelector || !targetSelector.startsWith('#')) {
                    return;
                }

                const pane = accordion.querySelector(targetSelector);

                if (!pane) {
                    return;
                }

                const isOpen = pane.classList.contains('show');

                closeAll();

                if (!isOpen) {
                    pane.classList.add('show');
                    trigger.setAttribute('aria-expanded', 'true');
                }
            });
        });
    }
});
</script>
@stop
