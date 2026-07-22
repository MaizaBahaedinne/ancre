@extends('adminlte::page')
@section('title', 'Detail Incident')
@section('content_header')<h1 class="m-0">Detail incident</h1>@stop
@section('content')
@php
	$workflowSteps = [
		'ouvert' => 'Ouvert',
		'pris_en_charge' => 'Pris en charge',
		'en_cours' => 'En cours',
		'en_attente' => 'En attente',
		'cloture' => 'Cloture',
	];

	$openedAt = $incident->opened_at ?? ($incident->date ? $incident->date->copy()->startOfDay() : null);
	$takenAt = $incident->taken_at;
	$resolvedAt = $incident->resolved_at;
	$closedAt = $incident->closed_at;
@endphp

<div class="card mb-4">
	<div class="card-body">
		<div class="d-flex flex-column flex-lg-row justify-content-between gap-3 align-items-lg-center">
			<div>
				<div class="d-flex align-items-center gap-2 flex-wrap mb-2">
					<span class="badge bg-{{ $incident->workflowBadgeClass() }} px-3 py-2">{{ \App\Models\Incident::WORKFLOW_OPTIONS[$incident->workflow_status] ?? ucfirst($incident->workflow_status) }}</span>
					<span class="text-muted">Incident #{{ $incident->id }}</span>
				</div>
				<h2 class="h4 mb-1">{{ $incident->type_incident }}</h2>
				<p class="text-muted mb-0">{{ $incident->enfant?->nom }} {{ $incident->enfant?->prenom }} · {{ optional($incident->date)->format('d/m/Y') }}</p>
			</div>

			<form method="POST" action="{{ route('incidents.update', $incident) }}" class="d-flex flex-column flex-md-row gap-2 align-items-md-end">
				@csrf
				@method('PUT')
				<input type="hidden" name="enfant_id" value="{{ $incident->enfant_id }}">
				<input type="hidden" name="date" value="{{ optional($incident->date)->format('Y-m-d') }}">
				<input type="hidden" name="type_incident" value="{{ $incident->type_incident }}">
				<input type="hidden" name="description" value="{{ e($incident->description) }}">
				<input type="hidden" name="action_realisee" value="{{ e($incident->action_realisee) }}">
				<div>
					<label class="form-label">Workflow</label>
					<select name="workflow_status" class="form-control">
						@foreach($workflowSteps as $workflowValue => $workflowLabel)
							<option value="{{ $workflowValue }}" @selected(old('workflow_status', $incident->workflow_status) === $workflowValue)>{{ $workflowLabel }}</option>
						@endforeach
					</select>
				</div>
				<div>
					<label class="form-label">Responsable</label>
					<select name="responsable_personnel_id" class="form-control" data-enhance-select="true">
						<option value="">Aucun</option>
						@foreach($personnels as $personnel)
							<option value="{{ $personnel->id }}" @selected((string) old('responsable_personnel_id', $incident->responsable_personnel_id) === (string) $personnel->id)>{{ $personnel->prenom }} {{ $personnel->nom }} - {{ $personnel->fonction }}</option>
						@endforeach
					</select>
				</div>
				<div>
					<button type="submit" class="btn btn-primary">Mettre a jour le workflow</button>
				</div>
			</form>
		</div>

		<div class="row g-3 mt-4">
			@foreach($workflowSteps as $workflowValue => $workflowLabel)
				@php
					$isCurrent = $incident->workflow_status === $workflowValue;
					$isDone = array_search($workflowValue, array_keys($workflowSteps), true) <= array_search($incident->workflow_status, array_keys($workflowSteps), true);
				@endphp
				<div class="col-12 col-md-4 col-xl">
					<div class="border rounded-4 p-3 h-100 {{ $isCurrent ? 'border-primary bg-primary-subtle' : ($isDone ? 'border-success bg-success-subtle' : 'border-light') }}">
						<div class="d-flex justify-content-between align-items-center mb-2">
							<strong>{{ $workflowLabel }}</strong>
							<span class="badge bg-{{ $isCurrent ? 'primary' : ($isDone ? 'success' : 'secondary') }}">{{ $isCurrent ? 'Actif' : ($isDone ? 'Passe' : 'A venir') }}</span>
						</div>
						<div class="small text-muted">
							@if($workflowValue === 'ouvert')
								Ouverture: {{ $openedAt ? $openedAt->format('d/m/Y H:i') : '-' }}
							@elseif($workflowValue === 'pris_en_charge')
								Prise en charge: {{ $takenAt ? $takenAt->format('d/m/Y H:i') : '-' }}
							@elseif($workflowValue === 'en_cours')
								Responsable: {{ $incident->responsablePersonnel ? $incident->responsablePersonnel->prenom.' '.$incident->responsablePersonnel->nom : '-' }}
							@elseif($workflowValue === 'en_attente')
								En attente de traitement.
							@else
								Resolution: {{ $resolvedAt ? $resolvedAt->format('d/m/Y H:i') : '-' }}
							@endif
						</div>
					</div>
				</div>
			@endforeach
		</div>
	</div>
</div>

<div class="card mb-4">
	<div class="card-header"><h3 class="card-title mb-0">Indicateurs de suivi</h3></div>
	<div class="card-body">
		<div class="row g-3">
			<div class="col-md-3">
				<div class="border rounded-4 p-3 h-100">
					<div class="text-muted small">Date d'ouverture</div>
					<div class="fw-bold">{{ $openedAt ? $openedAt->format('d/m/Y H:i') : '-' }}</div>
				</div>
			</div>
			<div class="col-md-3">
				<div class="border rounded-4 p-3 h-100">
					<div class="text-muted small">Temps de prise en charge</div>
					<div class="fw-bold">{{ $incident->open_to_taken_minutes !== null ? $incident->open_to_taken_minutes.' min' : '-' }}</div>
				</div>
			</div>
			<div class="col-md-3">
				<div class="border rounded-4 p-3 h-100">
					<div class="text-muted small">Date et heure de résolution</div>
					<div class="fw-bold">{{ $resolvedAt ? $resolvedAt->format('d/m/Y H:i') : '-' }}</div>
				</div>
			</div>
			<div class="col-md-3">
				<div class="border rounded-4 p-3 h-100">
					<div class="text-muted small">Temps pour la résolution</div>
					<div class="fw-bold">{{ $incident->open_to_resolved_minutes !== null ? $incident->open_to_resolved_minutes.' min' : '-' }}</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="card">
	<div class="card-header"><h3 class="card-title mb-0">Details de l'incident</h3></div>
	<div class="card-body">
		<dl class="row mb-0">
			<dt class="col-sm-4">Enfant</dt><dd class="col-sm-8">{{ $incident->enfant?->nom }} {{ $incident->enfant?->prenom }}</dd>
			<dt class="col-sm-4">Parent</dt><dd class="col-sm-8">{{ $incident->enfant?->parent?->nom }} {{ $incident->enfant?->parent?->prenom }}</dd>
			<dt class="col-sm-4">Personnel responsable</dt><dd class="col-sm-8">{{ $incident->responsablePersonnel ? $incident->responsablePersonnel->prenom.' '.$incident->responsablePersonnel->nom.' - '.$incident->responsablePersonnel->fonction : '-' }}</dd>
			<dt class="col-sm-4">Action realisee</dt><dd class="col-sm-8 modern-richtext-output">{!! $incident->action_realisee ?: '<p>-</p>' !!}</dd>
			<dt class="col-sm-4">Description</dt><dd class="col-sm-8 modern-richtext-output">{!! $incident->description !!}</dd>
			<dt class="col-sm-4">Pieces jointes</dt>
			<dd class="col-sm-8">
			@if(!empty($incident->attachments))
				<div class="modern-attachment-list">
					@foreach($incident->attachments as $attachment)
						<a href="{{ Storage::disk('public')->url($attachment['path']) }}" target="_blank" rel="noopener" class="modern-attachment-chip">
							<i class="fa-solid {{ str_starts_with($attachment['mime'] ?? '', 'image/') ? 'fa-image' : 'fa-file-lines' }}"></i>
							<span>{{ $attachment['name'] }}</span>
						</a>
					@endforeach
				</div>
			@else
				-
			@endif
			</dd>
		</dl>
	</div>
	<div class="card-footer">
		<a href="{{ route('incidents.edit', $incident) }}" class="btn btn-warning">Modifier</a>
		<a href="{{ route('incidents.index') }}" class="btn btn-secondary">Retour</a>
	</div>
</div>
@stop
