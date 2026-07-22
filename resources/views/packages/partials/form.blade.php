<div class="form-group">
    <label>Nom du package</label>
    <input type="text" name="nom" class="form-control @error('nom') is-invalid @enderror" value="{{ old('nom', optional($package)->nom) }}" required>
    @error('nom') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>

<div class="row">
    <div class="col-md-4 form-group">
        <label>Frais mensuels scolarite</label>
        <input type="number" step="0.01" min="0" name="frais_scolarite" class="form-control @error('frais_scolarite') is-invalid @enderror" value="{{ old('frais_scolarite', optional($package)->frais_scolarite ?? 0) }}" required>
        @error('frais_scolarite') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-4 form-group">
        <label>Frais mensuels dejeuner</label>
        <input type="number" step="0.01" min="0" name="frais_dejeuner" class="form-control @error('frais_dejeuner') is-invalid @enderror" value="{{ old('frais_dejeuner', optional($package)->frais_dejeuner ?? 0) }}" required>
        @error('frais_dejeuner') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-4 form-group">
        <label>Frais mensuels activite</label>
        <input type="number" step="0.01" min="0" name="frais_activite" class="form-control @error('frais_activite') is-invalid @enderror" value="{{ old('frais_activite', optional($package)->frais_activite ?? 0) }}" required>
        @error('frais_activite') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
</div>

<div class="form-group">
    <label>Statut</label>
    <select name="is_active" class="form-control @error('is_active') is-invalid @enderror" required>
        <option value="1" @selected((string) old('is_active', optional($package)->is_active ?? 1) === '1')>Actif</option>
        <option value="0" @selected((string) old('is_active', optional($package)->is_active) === '0')>Inactif</option>
    </select>
    @error('is_active') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>

<div class="alert alert-light border mb-0">
    <strong>Total mensuel:</strong>
    {{ number_format((float) old('frais_scolarite', optional($package)->frais_scolarite ?? 0) + (float) old('frais_dejeuner', optional($package)->frais_dejeuner ?? 0) + (float) old('frais_activite', optional($package)->frais_activite ?? 0), 2, ',', ' ') }} TND
</div>