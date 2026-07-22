@extends('adminlte::page')

@section('title', 'Inscription activite')

@section('content_header')
<div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
    <div>
        <h1 class="m-0">Inscription a l'activite</h1>
        <small class="text-muted">{{ $activite->titre }}</small>
    </div>
    <a href="{{ route('parent.activites.index') }}" class="btn btn-outline-secondary">Retour aux activites</a>
</div>
@stop

@section('content')
@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif
@if($errors->any())
    <div class="alert alert-danger mb-3">{{ $errors->first() }}</div>
@endif

<div class="row g-3">
    <div class="col-12 col-lg-5">
        <div class="card h-100">
            <div class="card-header"><h3 class="card-title mb-0">Informations activite</h3></div>
            <div class="card-body">
                <p class="mb-2"><strong>Date:</strong> {{ optional($activite->date)->format('d/m/Y') ?: '-' }}</p>
                <p class="mb-2"><strong>Heure:</strong> {{ $activite->heure_debut ?: $activite->heure ?: '-' }} @if($activite->heure_fin) - {{ $activite->heure_fin }} @endif</p>
                <p class="mb-2"><strong>Salle:</strong> {{ $activite->salle?->nom ?: '-' }}</p>
                <p class="mb-2"><strong>Frais:</strong> {{ $activite->frais_participation !== null ? number_format((float) $activite->frais_participation, 2, ',', ' ').' TND' : 'Aucun frais' }}</p>
                <p class="mb-0"><strong>Places validees:</strong> {{ $validatedCount }} / {{ $activite->capacite ?: 'Illimite' }}</p>
            </div>
        </div>
    </div>

    <div class="col-12 col-lg-7">
        <div class="card h-100">
            <div class="card-header"><h3 class="card-title mb-0">Inscrire un enfant</h3></div>
            <div class="card-body">
                @if(! $canAddParticipants)
                    <div class="alert alert-warning mb-0">
                        Les inscriptions sont fermees depuis la fin de l'activite{{ $activityEndAt ? ' le '.$activityEndAt->format('d/m/Y a H:i') : '' }}.
                    </div>
                @else
                <form method="POST" action="{{ route('parent.activites.registrations.store', $activite) }}" class="row g-3">
                    @csrf
                    <div class="col-md-6">
                        <label class="form-label">Enfant</label>
                        <select name="enfant_id" class="form-control" required>
                            <option value="">Choisir...</option>
                            @foreach($registrableChildren as $child)
                                <option value="{{ $child->id }}">{{ $child->prenom }} {{ $child->nom }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Type de paiement</label>
                        <select name="payment_reference" class="form-control @error('payment_reference') is-invalid @enderror">
                            <option value="">Choisir...</option>
                            @foreach($paymentMethodOptions as $value => $label)
                                <option value="{{ $value }}" @selected(old('payment_reference') === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('payment_reference') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-12">
                        <div class="form-check">
                            <input type="hidden" name="is_paid" value="0">
                            <input class="form-check-input" type="checkbox" id="is_paid" name="is_paid" value="1" @checked(old('is_paid'))>
                            <label class="form-check-label" for="is_paid">Paiement effectue (la validation depend du paiement et des places disponibles)</label>
                        </div>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Note (optionnelle)</label>
                        <textarea name="notes" class="form-control" rows="2">{{ old('notes') }}</textarea>
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary" @disabled($registrableChildren->isEmpty())>Enregistrer l'inscription</button>
                    </div>
                </form>
                @if($registrableChildren->isEmpty())
                    <div class="alert alert-info mt-3 mb-0">Tous les enfants disponibles sont deja inscrits a cette activite.</div>
                @endif
                @endif
            </div>
        </div>
    </div>
</div>

<div class="card mt-3">
    <div class="card-header"><h3 class="card-title mb-0">Mes inscriptions pour cette activite</h3></div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-striped mb-0">
                <thead>
                    <tr>
                        <th>Enfant</th>
                        <th>Statut</th>
                        <th>Paiement</th>
                        <th>Type paiement</th>
                        <th>Participation</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($children as $child)
                        @php $registration = $registrations[$child->id] ?? null; @endphp
                        <tr>
                            <td>{{ $child->prenom }} {{ $child->nom }}</td>
                            <td>
                                @if($registration)
                                    <span class="badge badge-{{ $registration->statusBadgeClass() }}">{{ $statusOptions[$registration->status] ?? $registration->status }}</span>
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                @if($registration?->paid_at)
                                    {{ number_format((float) $registration->amount_paid, 2, ',', ' ') }} TND
                                @else
                                    -
                                @endif
                            </td>
                            <td>{{ $registration?->payment_reference ?: '-' }}</td>
                            <td>{{ $registration?->participation_status ? ucfirst($registration->participation_status) : '-' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center">Aucun enfant rattache a ce compte.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@stop
