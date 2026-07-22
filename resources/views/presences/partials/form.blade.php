<div class="row">
    <div class="col-md-6 form-group">
        <label>Enfant</label>
        <select name="enfant_id" class="form-control @error('enfant_id') is-invalid @enderror" required>
            <option value="">Choisir...</option>
            @foreach($enfants as $e)
                <option value="{{ $e->id }}" @selected(old('enfant_id', optional($presence)->enfant_id) == $e->id)>{{ $e->nom }} {{ $e->prenom }}</option>
            @endforeach
        </select>
        @error('enfant_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-6 form-group">
        <label>Date</label>
        <input type="date" name="date" class="form-control @error('date') is-invalid @enderror" value="{{ old('date', optional(optional($presence)->date)->format('Y-m-d')) }}" required>
        @error('date') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
</div>

<div class="row">
    <div class="col-md-6 form-group">
        <label>Heure arrivee</label>
        <input type="time" name="heure_arrivee" class="form-control @error('heure_arrivee') is-invalid @enderror" value="{{ old('heure_arrivee', optional($presence)->heure_arrivee) }}">
        @error('heure_arrivee') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-6 form-group">
        <label>Heure depart</label>
        <input type="time" name="heure_depart" class="form-control @error('heure_depart') is-invalid @enderror" value="{{ old('heure_depart', optional($presence)->heure_depart) }}">
        @error('heure_depart') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
</div>

<div class="row">
    <div class="col-md-6 form-group">
        <label>Personne depot</label>
        <input type="text" name="personne_depot" class="form-control @error('personne_depot') is-invalid @enderror" value="{{ old('personne_depot', optional($presence)->personne_depot) }}">
        @error('personne_depot') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-6 form-group">
        <label>Personne retrait</label>
        <input type="text" name="personne_retrait" class="form-control @error('personne_retrait') is-invalid @enderror" value="{{ old('personne_retrait', optional($presence)->personne_retrait) }}">
        @error('personne_retrait') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
</div>

<div class="form-group">
    <label>Remarque</label>
    <textarea name="remarque" rows="2" class="form-control @error('remarque') is-invalid @enderror">{{ old('remarque', optional($presence)->remarque) }}</textarea>
    @error('remarque') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>
