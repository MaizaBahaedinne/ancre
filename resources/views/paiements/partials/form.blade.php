<div class="row">
<div class="col-md-6 form-group">
<label>Enfant</label>
<select name="enfant_id" class="form-control @error('enfant_id') is-invalid @enderror" required>
<option value="">Choisir...</option>
@foreach($enfants as $e)
<option value="{{ $e->id }}" @selected(old('enfant_id', optional($paiement)->enfant_id) == $e->id)>{{ $e->nom }} {{ $e->prenom }}</option>
@endforeach
</select>
@error('enfant_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>
<div class="col-md-6 form-group">
<label>Montant (TND)</label>
<input type="number" step="0.01" min="0" name="montant" class="form-control @error('montant') is-invalid @enderror" value="{{ old('montant', optional($paiement)->montant) }}" required>
@error('montant') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>
</div>
<div class="row">
<div class="col-md-4 form-group"><label>Date paiement</label><input type="date" name="date_paiement" class="form-control @error('date_paiement') is-invalid @enderror" value="{{ old('date_paiement', optional(optional($paiement)->date_paiement)->format('Y-m-d')) }}" required>@error('date_paiement') <div class="invalid-feedback">{{ $message }}</div> @enderror</div>
<div class="col-md-4 form-group"><label>Mois</label><input type="number" min="1" max="12" name="mois" class="form-control @error('mois') is-invalid @enderror" value="{{ old('mois', optional($paiement)->mois) }}" required>@error('mois') <div class="invalid-feedback">{{ $message }}</div> @enderror</div>
<div class="col-md-4 form-group"><label>Annee</label><input type="number" min="2000" max="2100" name="annee" class="form-control @error('annee') is-invalid @enderror" value="{{ old('annee', optional($paiement)->annee) }}" required>@error('annee') <div class="invalid-feedback">{{ $message }}</div> @enderror</div>
</div>
<div class="row">
<div class="col-md-6 form-group"><label>Mode paiement</label><select name="mode_paiement" class="form-control @error('mode_paiement') is-invalid @enderror" required>@foreach(['Especes','Carte','Virement','Cheque'] as $mode)<option value="{{ $mode }}" @selected(old('mode_paiement', optional($paiement)->mode_paiement) === $mode)>{{ $mode }}</option>@endforeach</select>@error('mode_paiement') <div class="invalid-feedback">{{ $message }}</div> @enderror</div>
<div class="col-md-6 form-group"><label>Statut</label><select name="statut" class="form-control @error('statut') is-invalid @enderror" required>@foreach(['Paye','En retard','Partiel'] as $s)<option value="{{ $s }}" @selected(old('statut', optional($paiement)->statut ?? 'Paye') === $s)>{{ $s }}</option>@endforeach</select>@error('statut') <div class="invalid-feedback">{{ $message }}</div> @enderror</div>
</div>
<div class="form-group">
<label>Commentaire</label>
<textarea name="commentaire" rows="2" class="form-control @error('commentaire') is-invalid @enderror">{{ old('commentaire', optional($paiement)->commentaire) }}</textarea>
@error('commentaire') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>
