@php
	$selectedRecurrence = old('recurrence', optional($activite)->recurrence);
	$selectedRecurrenceDays = old('recurrence_jours', optional($activite)->recurrence_jours ?? []);
	$selectedDayOfMonth = old('recurrence_jour_mois', optional($activite)->recurrence_jour_mois);
	$selectedAnnualDate = old('recurrence_date_annuelle', optional(optional($activite)->recurrence_date_annuelle)->format('Y-m-d'));
	$activityDate = old('date', optional(optional($activite)->date)->format('Y-m-d'));
	$minimumRecurrenceEndDate = now()->addDay()->toDateString();

	if (!empty($activityDate) && $activityDate > $minimumRecurrenceEndDate) {
		$minimumRecurrenceEndDate = $activityDate;
	}

	$weekDays = [
		'lundi' => 'Lundi',
		'mardi' => 'Mardi',
		'mercredi' => 'Mercredi',
		'jeudi' => 'Jeudi',
		'vendredi' => 'Vendredi',
		'samedi' => 'Samedi',
		'dimanche' => 'Dimanche',
	];
@endphp
<div class="form-group">
<label>Titre</label>
<input type="text" name="titre" class="form-control @error('titre') is-invalid @enderror" value="{{ old('titre', optional($activite)->titre) }}" required>
@error('titre') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>
<div class="row">
<div class="col-md-6 form-group">
<label>Date</label>
<input type="date" name="date" min="{{ now()->toDateString() }}" class="form-control @error('date') is-invalid @enderror" value="{{ old('date', optional(optional($activite)->date)->format('Y-m-d')) }}" required>
@error('date') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>
<div class="col-md-3 form-group">
<label>Heure de debut</label>
<input type="time" name="heure_debut" class="form-control @error('heure_debut') is-invalid @enderror" value="{{ old('heure_debut', optional($activite)->heure_debut ?? optional($activite)->heure) }}" required>
@error('heure_debut') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>
<div class="col-md-3 form-group">
<label>Heure de fin</label>
<input type="time" name="heure_fin" class="form-control @error('heure_fin') is-invalid @enderror" value="{{ old('heure_fin', optional($activite)->heure_fin) }}" required>
@error('heure_fin') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>
</div>
<div class="row">
<div class="col-md-6 form-group">
<label>Recurrence</label>
<select name="recurrence" class="form-control @error('recurrence') is-invalid @enderror" data-recurrence-select>
<option value="">Aucune</option>
@foreach($recurrenceOptions as $recurrenceValue => $recurrenceLabel)
<option value="{{ $recurrenceValue }}" @selected($selectedRecurrence === $recurrenceValue)>{{ $recurrenceLabel }}</option>
@endforeach
</select>
@error('recurrence') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>
<div class="col-md-6 form-group">
<label>Date de fin de recurrence</label>
<input type="date" name="date_fin_recurrence" min="{{ $minimumRecurrenceEndDate }}" class="form-control @error('date_fin_recurrence') is-invalid @enderror" value="{{ old('date_fin_recurrence', optional(optional($activite)->date_fin_recurrence)->format('Y-m-d')) }}">
@error('date_fin_recurrence') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>
</div>
<div class="form-group" data-recurrence-weekly @style($selectedRecurrence === 'hebdomadaire' ? '' : 'display:none;')>
<label>Jours de repetition</label>
<div class="d-flex flex-wrap gap-3">
@foreach($weekDays as $dayValue => $dayLabel)
<div class="form-check">
<input class="form-check-input" type="checkbox" name="recurrence_jours[]" value="{{ $dayValue }}" id="recurrence_{{ $dayValue }}" @checked(in_array($dayValue, $selectedRecurrenceDays, true))>
<label class="form-check-label" for="recurrence_{{ $dayValue }}">{{ $dayLabel }}</label>
</div>
@endforeach
</div>
@error('recurrence_jours') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
</div>
<div class="row" data-recurrence-monthly @style(in_array($selectedRecurrence, ['mensuelle', 'trimestrielle', 'semestrielle'], true) ? '' : 'display:none;')>
<div class="col-md-6 form-group">
<label>Jour du mois</label>
<select name="recurrence_jour_mois" class="form-control @error('recurrence_jour_mois') is-invalid @enderror" data-enhance-select="true">
<option value="">Choisir un jour...</option>
@for($dayOfMonth = 1; $dayOfMonth <= 31; $dayOfMonth++)
<option value="{{ $dayOfMonth }}" @selected((string) $selectedDayOfMonth === (string) $dayOfMonth)>{{ $dayOfMonth }}</option>
@endfor
</select>
@error('recurrence_jour_mois') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>
</div>
<div class="row" data-recurrence-annual @style($selectedRecurrence === 'annuelle' ? '' : 'display:none;')>
<div class="col-md-6 form-group">
<label>Date exacte annuelle</label>
<input type="date" name="recurrence_date_annuelle" min="{{ old('date', optional(optional($activite)->date)->format('Y-m-d')) ?: now()->toDateString() }}" class="form-control @error('recurrence_date_annuelle') is-invalid @enderror" value="{{ $selectedAnnualDate }}">
@error('recurrence_date_annuelle') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>
</div>
<div class="row">
<div class="col-md-6 form-group">
<label>Responsable</label>
<select name="responsable_personnel_id" class="form-control @error('responsable_personnel_id') is-invalid @enderror" required data-enhance-select="true">
<option value="">Choisir...</option>
@foreach($responsables as $responsable)
<option value="{{ $responsable->id }}" @selected((string) old('responsable_personnel_id', optional($activite)->responsable_personnel_id) === (string) $responsable->id)>{{ $responsable->prenom }} {{ $responsable->nom }} - {{ $responsable->fonction }}</option>
@endforeach
</select>
<input type="hidden" name="responsable" value="{{ old('responsable', optional($activite)->responsable) }}">
@error('responsable_personnel_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
@error('responsable') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>
<div class="col-md-6 form-group">
<label>Salle</label>
<select name="salle_id" class="form-control @error('salle_id') is-invalid @enderror" required data-enhance-select="true">
<option value="">Choisir...</option>
@foreach($salles as $salle)
<option value="{{ $salle->id }}" @selected((string) old('salle_id', optional($activite)->salle_id) === (string) $salle->id)>{{ $salle->nom }} - Etage {{ $salle->etage }} - Cap. {{ $salle->capacite }}</option>
@endforeach
</select>
@error('salle_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>
</div>
<div class="row">
<div class="col-md-3 form-group">
<label>Capacite</label>
<input type="number" name="capacite" min="1" class="form-control @error('capacite') is-invalid @enderror" value="{{ old('capacite', optional($activite)->capacite) }}">
@error('capacite') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>
<div class="col-md-3 form-group">
<label>Frais de participation</label>
<input type="number" step="0.01" min="0" name="frais_participation" class="form-control @error('frais_participation') is-invalid @enderror" value="{{ old('frais_participation', optional($activite)->frais_participation) }}">
@error('frais_participation') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>
</div>
<div class="form-group">
<label>Description</label>
<input type="hidden" id="description_activite" name="description" value="{{ old('description', optional($activite)->description) }}">
<div class="modern-rich-editor @error('description') is-invalid @enderror" data-rich-editor data-input="description_activite" data-placeholder="Decrivez l'activite..."></div>
@error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>

@once
<script>
document.addEventListener('DOMContentLoaded', () => {
	const recurrenceSelect = document.querySelector('[data-recurrence-select]');

	if (!recurrenceSelect) {
		return;
	}

	const weeklyBlock = document.querySelector('[data-recurrence-weekly]');
	const monthlyBlock = document.querySelector('[data-recurrence-monthly]');
	const annualBlock = document.querySelector('[data-recurrence-annual]');

	const syncRecurrenceBlocks = () => {
		const recurrence = recurrenceSelect.value;

		if (weeklyBlock) {
			weeklyBlock.style.display = recurrence === 'hebdomadaire' ? '' : 'none';
		}

		if (monthlyBlock) {
			monthlyBlock.style.display = ['mensuelle', 'trimestrielle', 'semestrielle'].includes(recurrence) ? '' : 'none';
		}

		if (annualBlock) {
			annualBlock.style.display = recurrence === 'annuelle' ? '' : 'none';
		}
	};

	recurrenceSelect.addEventListener('change', syncRecurrenceBlocks);
	syncRecurrenceBlocks();
});
</script>
@endonce
