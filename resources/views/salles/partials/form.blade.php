<div class="row">
<div class="col-md-6 form-group">
<label>Nom</label>
<input type="text" name="nom" class="form-control @error('nom') is-invalid @enderror" value="{{ old('nom', optional($salle)->nom) }}" required>
@error('nom') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>
<div class="col-md-3 form-group">
<label>Etage</label>
<input type="text" name="etage" class="form-control @error('etage') is-invalid @enderror" value="{{ old('etage', optional($salle)->etage) }}" required placeholder="RDC, 1, 2...">
@error('etage') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>
<div class="col-md-3 form-group">
<label>Capacite</label>
<input type="number" name="capacite" min="1" class="form-control @error('capacite') is-invalid @enderror" value="{{ old('capacite', optional($salle)->capacite) }}" required>
@error('capacite') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>
</div>

<div class="row">
<div class="col-md-6 form-group">
<label>Statut</label>
<select name="statut" class="form-control @error('statut') is-invalid @enderror" required>
@foreach($statutOptions as $statutValue => $statutLabel)
<option value="{{ $statutValue }}" @selected(old('statut', optional($salle)->statut ?? \App\Models\Salle::STATUT_DISPONIBLE) === $statutValue)>{{ $statutLabel }}</option>
@endforeach
</select>
@error('statut') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>
<div class="col-md-6 form-group">
<label>Responsable</label>
<select name="responsable_personnel_id" class="form-control @error('responsable_personnel_id') is-invalid @enderror" data-enhance-select="true">
<option value="">Aucun</option>
@foreach($responsables as $responsable)
<option value="{{ $responsable->id }}" @selected((string) old('responsable_personnel_id', optional($salle)->responsable_personnel_id) === (string) $responsable->id)>{{ $responsable->prenom }} {{ $responsable->nom }} - {{ $responsable->fonction }}</option>
@endforeach
</select>
@error('responsable_personnel_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>
</div>

<div class="form-group">
<label>Equipements</label>
<div class="d-flex flex-wrap gap-3">
@foreach($equipementOptions as $equipementValue => $equipementLabel)
<div class="form-check">
<input class="form-check-input" type="checkbox" name="equipements[]" value="{{ $equipementValue }}" id="equipement_{{ $equipementValue }}" @checked(in_array($equipementValue, old('equipements', optional($salle)->equipements ?? []), true))>
<label class="form-check-label" for="equipement_{{ $equipementValue }}">{{ $equipementLabel }}</label>
</div>
@endforeach
</div>
@error('equipements') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
@error('equipements.*') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
</div>
