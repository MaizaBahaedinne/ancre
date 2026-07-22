@extends('adminlte::page')

@section('title', 'Detail Activite')

@section('content_header')
<div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
	<div>
		<h1 class="m-0">Detail activite</h1>
		<small class="text-muted">Vue detaillee et suivi des inscriptions</small>
	</div>
	<a href="{{ route('activites.index') }}" class="btn btn-outline-secondary">Retour aux activites</a>
</div>
@stop

@section('content')
@php
	$recurrenceDaysLabels = [
		'lundi' => 'Lundi',
		'mardi' => 'Mardi',
		'mercredi' => 'Mercredi',
		'jeudi' => 'Jeudi',
		'vendredi' => 'Vendredi',
		'samedi' => 'Samedi',
		'dimanche' => 'Dimanche',
	];
	$recurrenceLabels = [
		'journalier' => 'Journaliere',
		'hebdomadaire' => 'Hebdomadaire',
		'mensuelle' => 'Mensuelle',
		'trimestrielle' => 'Trimestrielle',
		'semestrielle' => 'Semestrielle',
		'annuelle' => 'Annuelle',
	];
	$registrations = $activite->registrations;
	$startTime = $activite->heure_debut ?: $activite->heure;
	$endTime = $activite->heure_fin;
	$scheduleLabel = $startTime ? $startTime.($endTime ? ' - '.$endTime : '') : 'Horaire non defini';
	$recurrenceLabel = $recurrenceLabels[$activite->recurrence] ?? ($activite->recurrence ? ucfirst($activite->recurrence) : 'Ponctuelle');
	$recurrenceDays = collect($activite->recurrence_jours ?? [])->map(fn ($day) => $recurrenceDaysLabels[$day] ?? ucfirst($day))->join(', ');
	$responsableLabel = $activite->responsablePersonnel
		? $activite->responsablePersonnel->prenom.' '.$activite->responsablePersonnel->nom.' - '.$activite->responsablePersonnel->fonction
		: ($activite->responsable ?: '-');
	$salleLabel = $activite->salle?->nom ? $activite->salle->nom.' - Etage '.$activite->salle->etage : '-';
	$totalRegistrations = $registrations->count();
	$validatedCount = $registrations->where('status', \App\Models\ActivityRegistration::STATUS_VALIDATED)->count();
	$pendingCount = $registrations->where('status', \App\Models\ActivityRegistration::STATUS_PENDING_PAYMENT)->count();
	$waitlistCount = $registrations->where('status', \App\Models\ActivityRegistration::STATUS_WAITLIST)->count();
	$cancelledCount = $registrations->where('status', \App\Models\ActivityRegistration::STATUS_CANCELLED)->count();
	$participatedCount = $registrations->where('participation_status', \App\Models\ActivityRegistration::PARTICIPATION_PRESENT)->count();
	$capacityLimit = $activite->capacite ?: null;
	$capacityClass = $capacityLimit && $validatedCount >= $capacityLimit ? 'is-alert' : 'is-safe';
	$paidTotal = $registrations->filter(fn ($registration) => ! is_null($registration->paid_at))->sum(fn ($registration) => (float) $registration->amount_paid);
@endphp

@if(session('success'))
	<div class="alert alert-success">{{ session('success') }}</div>
@endif

@if(session('error'))
	<div class="alert alert-danger">{{ session('error') }}</div>
@endif

<div class="child-profile-shell activity-show-shell">
	<section class="child-profile-hero activity-show-hero card">
		<div class="card-body">
			<div class="child-profile-hero-grid activity-show-hero-grid">
				<div class="activity-show-icon-wrap">
					<div class="activity-show-icon">
						<i class="fa-solid fa-calendar-star"></i>
					</div>
				</div>
				<div>
					<p class="child-profile-kicker">Programmation activite</p>
					<h2 class="child-profile-name">{{ $activite->titre }}</h2>
					<div class="child-profile-meta">
						<span><i class="fa-solid fa-calendar-days"></i>{{ optional($activite->date)->format('d/m/Y') ?: 'Date non definie' }}</span>
						<span><i class="fa-solid fa-clock"></i>{{ $scheduleLabel }}</span>
						<span><i class="fa-solid fa-location-dot"></i>{{ $salleLabel }}</span>
					</div>
					<div class="child-profile-tags">
						<span class="child-profile-chip"><i class="fa-solid fa-repeat"></i>{{ $recurrenceLabel }}</span>
						<span class="child-profile-chip {{ $capacityClass }}"><i class="fa-solid fa-users"></i>{{ $validatedCount }} / {{ $capacityLimit ?: 'Illimite' }} places validees</span>
						<span class="child-profile-chip"><i class="fa-solid fa-wallet"></i>{{ $activite->frais_participation !== null ? number_format((float) $activite->frais_participation, 2, ',', ' ').' TND' : 'Sans frais' }}</span>
						<span class="child-profile-chip"><i class="fa-solid fa-user-check"></i>{{ $participatedCount }} participation(s) confirmee(s)</span>
					</div>
				</div>
				<div class="child-profile-actions">
					<a href="{{ route('activites.edit', $activite) }}" class="btn btn-warning">Modifier</a>
					<a href="{{ route('activites.index') }}" class="btn btn-secondary">Retour</a>
				</div>
			</div>
		</div>
	</section>

	<div class="row g-3">
		<div class="col-xl-3 col-md-6">
			<div class="child-stat-card">
				<div class="child-stat-icon activity-stat-icon is-primary"><i class="fa-solid fa-file-signature"></i></div>
				<div>
					<small>Inscriptions</small>
					<strong>{{ $totalRegistrations }}</strong>
					<span>Total enregistre</span>
				</div>
			</div>
		</div>
		<div class="col-xl-3 col-md-6">
			<div class="child-stat-card">
				<div class="child-stat-icon activity-stat-icon is-success"><i class="fa-solid fa-badge-check"></i></div>
				<div>
					<small>Validees</small>
					<strong>{{ $validatedCount }}</strong>
					<span>Places confirmees</span>
				</div>
			</div>
		</div>
		<div class="col-xl-3 col-md-6">
			<div class="child-stat-card">
				<div class="child-stat-icon activity-stat-icon is-warning"><i class="fa-solid fa-hourglass-half"></i></div>
				<div>
					<small>En attente</small>
					<strong>{{ $pendingCount }}</strong>
					<span>A valider</span>
				</div>
			</div>
		</div>
		<div class="col-xl-3 col-md-6">
			<div class="child-stat-card">
				<div class="child-stat-icon activity-stat-icon is-dark"><i class="fa-solid fa-money-bill-wave"></i></div>
				<div>
					<small>Encaisse</small>
					<strong>{{ number_format($paidTotal, 2, ',', ' ') }} TND</strong>
						<span>{{ $cancelledCount }} annulee(s)</span>
				</div>
			</div>
		</div>
	</div>

	<div class="row g-4 mt-1">
		<div class="col-lg-4">
			<div class="card child-profile-side-card h-100">
				<div class="card-body">
					<ul class="child-profile-facts list-unstyled mb-0">
						<li><i class="fa-solid fa-user-tie"></i><span>Responsable</span><strong>{{ $responsableLabel }}</strong></li>
						<li><i class="fa-solid fa-door-open"></i><span>Salle</span><strong>{{ $salleLabel }}</strong></li>
						<li><i class="fa-solid fa-arrows-rotate"></i><span>Recurrence</span><strong>{{ $recurrenceLabel }}</strong></li>
						<li><i class="fa-solid fa-calendar-check"></i><span>Jours</span><strong>{{ $recurrenceDays ?: '-' }}</strong></li>
						<li><i class="fa-solid fa-calendar-day"></i><span>Jour du mois</span><strong>{{ $activite->recurrence_jour_mois ?: '-' }}</strong></li>
						<li><i class="fa-solid fa-calendar-plus"></i><span>Date annuelle</span><strong>{{ optional($activite->recurrence_date_annuelle)->format('d/m/Y') ?: '-' }}</strong></li>
						<li><i class="fa-solid fa-calendar-xmark"></i><span>Fin recurrence</span><strong>{{ optional($activite->date_fin_recurrence)->format('d/m/Y') ?: '-' }}</strong></li>
						<li><i class="fa-solid fa-layer-group"></i><span>Capacite</span><strong>{{ $capacityLimit ?: 'Illimite' }}</strong></li>
					</ul>
				</div>
			</div>
		</div>

		<div class="col-lg-8">
			@can('activities.update')
			<div class="card mb-4">
				<div class="card-body child-profile-panel">
					<h4>Ajouter un participant</h4>
					<p class="child-profile-note activity-show-helper mb-3">Le parent peut inscrire ses propres enfants depuis son portail. Ici, l'admin et le responsable de cette activite peuvent ajouter n'importe quel enfant rattache a un parent.</p>
					@if(! $canAddParticipants)
						<div class="alert alert-warning mb-0">
							Le formulaire d'ajout est ferme depuis la fin de l'activite{{ $activityEndAt ? ' le '.$activityEndAt->format('d/m/Y a H:i') : '' }}.
						</div>
					@elseif($availableChildren->isEmpty())
						<div class="alert alert-info mb-0">Tous les enfants rattaches a un parent sont deja inscrits a cette activite.</div>
					@else
					<form method="POST" action="{{ route('activites.registrations.store', $activite) }}" class="row g-3 align-items-end">
						@csrf
						<div class="col-md-6">
							<label class="form-label">Enfant</label>
							<select name="enfant_id" class="form-control @error('enfant_id') is-invalid @enderror" required data-enhance-select="true">
								<option value="">Choisir...</option>
								@foreach($availableChildren as $child)
									<option value="{{ $child->id }}" @selected((string) old('enfant_id') === (string) $child->id)>
										{{ $child->prenom }} {{ $child->nom }}{{ $child->parent ? ' - '.$child->parent->prenom.' '.$child->parent->nom : '' }}
									</option>
								@endforeach
							</select>
							@error('enfant_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
						</div>
						<div class="col-md-3">
							<label class="form-label">Type de paiement</label>
							<select name="payment_reference" class="form-control @error('payment_reference') is-invalid @enderror">
								<option value="">Choisir...</option>
								@foreach($paymentMethodOptions as $value => $label)
									<option value="{{ $value }}" @selected(old('payment_reference') === $value)>{{ $label }}</option>
								@endforeach
							</select>
							@error('payment_reference') <div class="invalid-feedback">{{ $message }}</div> @enderror
						</div>
						<div class="col-md-3">
							<div class="form-check mt-4 pt-2">
								<input type="hidden" name="is_paid" value="0">
								<input class="form-check-input" type="checkbox" id="staff_is_paid" name="is_paid" value="1" @checked(old('is_paid'))>
								<label class="form-check-label" for="staff_is_paid">Paiement recu</label>
							</div>
						</div>
						<div class="col-12">
							<label class="form-label">Note</label>
							<textarea name="notes" rows="2" class="form-control @error('notes') is-invalid @enderror" placeholder="Optionnel">{{ old('notes') }}</textarea>
							@error('notes') <div class="invalid-feedback">{{ $message }}</div> @enderror
						</div>
						<div class="col-12 d-flex justify-content-end">
							<button type="submit" class="btn btn-primary">Ajouter le participant</button>
						</div>
					</form>
					@endif
				</div>
			</div>
			@endcan

			<div class="card mb-4">
				<div class="card-body child-profile-panel">
					<h4>Description</h4>
					<div class="modern-richtext-output activity-show-description">{!! $activite->description ?: '<p>Aucune description disponible.</p>' !!}</div>
				</div>
			</div>

			<div class="card">
				<div class="card-body child-profile-panel">
					<h4>Cadence et organisation</h4>
					<div class="row g-3">
						<div class="col-md-6">
							<div class="activity-show-note">
								<span class="activity-show-note-label">Planification</span>
								<strong>{{ optional($activite->date)->format('d/m/Y') ?: '-' }}</strong>
								<p>{{ $scheduleLabel }}</p>
							</div>
						</div>
						<div class="col-md-6">
							<div class="activity-show-note">
								<span class="activity-show-note-label">Accueil</span>
								<strong>{{ $salleLabel }}</strong>
								<p>{{ $responsableLabel }}</p>
							</div>
						</div>
						<div class="col-md-6">
							<div class="activity-show-note">
								<span class="activity-show-note-label">Participation</span>
								<strong>{{ $validatedCount }} validee(s)</strong>
								<p>{{ $pendingCount }} en attente de paiement, {{ $waitlistCount }} en liste d'attente</p>
							</div>
						</div>
						<div class="col-md-6">
							<div class="activity-show-note">
								<span class="activity-show-note-label">Budget</span>
								<strong>{{ $activite->frais_participation !== null ? number_format((float) $activite->frais_participation, 2, ',', ' ').' TND' : 'Aucun frais' }}</strong>
								<p>{{ number_format($paidTotal, 2, ',', ' ') }} TND encaisses a ce jour</p>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="card">
		<div class="card-header border-0 pt-4 px-4 pb-0 bg-transparent d-flex justify-content-between align-items-center flex-wrap gap-2">
			<div>
				<p class="child-profile-kicker mb-1">Suivi des inscriptions</p>
				<h3 class="card-title mb-0">Inscriptions et participation</h3>
				@if($participationCutoffAt)
					<small class="text-muted d-block mt-1">Action groupee disponible jusqu'au {{ $participationCutoffAt->format('d/m/Y a H:i') }}</small>
				@endif
			</div>
			<div class="child-profile-badges">
				<span class="child-profile-badge"><i class="fa-solid fa-users"></i>{{ $totalRegistrations }} inscription(s)</span>
				<span class="child-profile-badge"><i class="fa-solid fa-credit-card"></i>{{ number_format($paidTotal, 2, ',', ' ') }} TND</span>
			</div>
		</div>
		<div class="card-body pt-3">
			@can('activities.update')
				@if($canManageParticipation)
					<div class="activity-show-batch-actions mb-3">
						<form method="POST" action="{{ route('activites.registrations.participation.batch', $activite) }}">
							@csrf
							@method('PATCH')
							<input type="hidden" name="participation_status" value="present">
							<button type="submit" class="btn btn-outline-success">Tout marquer present</button>
						</form>
						<form method="POST" action="{{ route('activites.registrations.participation.batch', $activite) }}">
							@csrf
							@method('PATCH')
							<input type="hidden" name="participation_status" value="absent">
							<button type="submit" class="btn btn-outline-secondary">Tout marquer absent</button>
						</form>
					</div>
				@else
					<div class="alert alert-warning mb-3">La saisie de presence est fermee plus d'une heure apres la fin de l'activite.</div>
				@endif
			@endcan
			<form method="POST" action="{{ route('activites.registrations.participation.batch', $activite) }}">
				@csrf
				@method('PATCH')
			<div class="table-responsive">
				<table class="table table-striped table-bordered js-data-table nowrap align-middle">
					<thead>
						<tr>
							<th>Enfant</th>
							<th>Parent</th>
							<th>Statut inscription</th>
							<th>Paiement</th>
							<th>Type paiement</th>
							<th>Participation</th>
							<th width="220" class="no-sort">Action</th>
						</tr>
					</thead>
					<tbody>
						@forelse($registrations as $registration)
							<tr>
								<td>
									<div class="activity-show-person">
										<strong>{{ $registration->enfant?->prenom }} {{ $registration->enfant?->nom }}</strong>
										<small>#{{ $registration->id }}</small>
									</div>
								</td>
								<td>{{ $registration->parent?->prenom }} {{ $registration->parent?->nom }}</td>
								<td>
									<span class="badge badge-{{ $registration->statusBadgeClass() }}">
										{{ $registrationStatusOptions[$registration->status] ?? $registration->status }}
									</span>
								</td>
								<td>
									@if($registration->paid_at)
										<div class="activity-show-money">{{ number_format((float) $registration->amount_paid, 2, ',', ' ') }} TND</div>
										<small class="text-muted">Paye</small>
									@else
										<span class="text-muted">-</span>
									@endif
								</td>
								<td>{{ $registration->payment_reference ?: '-' }}</td>
								<td>
									@if($registration->participation_status)
										<span class="child-profile-chip activity-show-participation-pill">{{ ucfirst($registration->participation_status) }}</span>
									@else
										<span class="text-muted">-</span>
									@endif
								</td>
								<td>
									@can('activities.update')
											<select name="participation_statuses[{{ $registration->id }}]" class="form-control form-control-sm" @disabled($registration->status !== \App\Models\ActivityRegistration::STATUS_VALIDATED || ! $canManageParticipation)>
												<option value="">-</option>
												@foreach($participationOptions as $value => $label)
													<option value="{{ $value }}" @selected($registration->participation_status === $value)>{{ $label }}</option>
												@endforeach
											</select>
									@else
										-
									@endcan
								</td>
							</tr>
						@empty
							<tr><td colspan="7" class="text-center">Aucune inscription pour le moment.</td></tr>
						@endforelse
					</tbody>
				</table>
			</div>
				@can('activities.update')
					@if($canManageParticipation)
						<div class="d-flex justify-content-end mt-3">
							<button type="submit" class="btn btn-primary">Enregistrer toute la liste de presence</button>
						</div>
					@endif
				@endcan
			</form>
		</div>
	</div>
</div>
@stop

@section('css')
<style>
	.activity-show-shell {
		gap: 1.25rem;
	}

	.activity-show-hero {
		background:
			radial-gradient(circle at top right, rgba(249, 115, 22, 0.14), transparent 24%),
			radial-gradient(circle at left center, rgba(14, 165, 233, 0.12), transparent 30%),
			linear-gradient(180deg, rgba(255, 255, 255, 0.98), rgba(248, 250, 252, 0.96));
	}

	.activity-show-hero-grid {
		grid-template-columns: 120px minmax(0, 1fr) auto;
	}

	.activity-show-icon-wrap {
		display: flex;
		justify-content: center;
	}

	.activity-show-icon {
		display: inline-flex;
		align-items: center;
		justify-content: center;
		width: 7rem;
		height: 7rem;
		border-radius: 30px;
		background: linear-gradient(135deg, #0f172a, #f97316);
		color: #fff;
		font-size: 2.1rem;
		box-shadow: 0 24px 50px rgba(15, 23, 42, 0.16);
	}

	.activity-stat-icon.is-primary {
		background: linear-gradient(135deg, #0f172a, #2563eb);
	}

	.activity-stat-icon.is-success {
		background: linear-gradient(135deg, #166534, #22c55e);
	}

	.activity-stat-icon.is-warning {
		background: linear-gradient(135deg, #9a3412, #f97316);
	}

	.activity-stat-icon.is-dark {
		background: linear-gradient(135deg, #334155, #0f172a);
	}

	.activity-show-description {
		color: #334155;
		line-height: 1.8;
	}

	.activity-show-helper {
		margin-top: 0;
		padding-top: 0;
		border-top: 0;
	}

	.activity-show-note {
		height: 100%;
		padding: 1rem;
		border-radius: 18px;
		border: 1px solid #e2e8f0;
		background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
	}

	.activity-show-note-label {
		display: block;
		margin-bottom: 0.4rem;
		color: #94a3b8;
		font-size: 0.78rem;
		font-weight: 800;
		letter-spacing: 0.06em;
		text-transform: uppercase;
	}

	.activity-show-note strong {
		display: block;
		margin-bottom: 0.35rem;
		font-size: 1rem;
		color: #0f172a;
	}

	.activity-show-note p {
		margin: 0;
		color: #64748b;
	}

	.activity-show-person {
		display: grid;
		gap: 0.15rem;
	}

	.activity-show-person strong,
	.activity-show-money {
		color: #0f172a;
		font-weight: 700;
	}

	.activity-show-person small {
		color: #94a3b8;
		font-weight: 600;
	}

	.activity-show-participation-pill {
		padding-inline: 0.75rem;
		font-size: 0.8rem;
	}

	.activity-show-form {
		display: grid;
		grid-template-columns: minmax(0, 1fr) auto;
		gap: 0.5rem;
		align-items: center;
	}

	.activity-show-batch-actions {
		display: flex;
		flex-wrap: wrap;
		gap: 0.75rem;
	}

	@media (max-width: 991.98px) {
		.activity-show-hero-grid {
			grid-template-columns: 1fr;
		}

		.activity-show-form {
			grid-template-columns: 1fr;
		}
	}
</style>
@stop
