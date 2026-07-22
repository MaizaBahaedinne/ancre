@php
    $classRows = old('classes', optional($school)->classes?->map(fn ($class) => [
        'academic_year_id' => $class->academic_year_id,
        'name' => $class->name,
        'level' => $class->level,
        'capacity' => $class->capacity,
        'is_active' => $class->is_active,
    ])->values()->all() ?? [[]]);
@endphp
<div class="row">
<div class="col-md-6 form-group"><label>Nom</label><input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', optional($school)->name) }}" required>@error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror</div>
<div class="col-md-3 form-group"><label>Ville</label><input type="text" name="city" class="form-control @error('city') is-invalid @enderror" value="{{ old('city', optional($school)->city) }}">@error('city') <div class="invalid-feedback">{{ $message }}</div> @enderror</div>
<div class="col-md-3 form-group"><label>Telephone</label><input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone', optional($school)->phone) }}">@error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror</div>
</div>

<div class="row">
<div class="col-md-6 form-group"><label>Nom du directeur</label><input type="text" name="director_name" class="form-control @error('director_name') is-invalid @enderror" value="{{ old('director_name', optional($school)->director_name) }}">@error('director_name') <div class="invalid-feedback">{{ $message }}</div> @enderror</div>
<div class="col-md-6 form-group"><label>Contact directeur</label><input type="text" name="director_contact" class="form-control @error('director_contact') is-invalid @enderror" value="{{ old('director_contact', optional($school)->director_contact) }}">@error('director_contact') <div class="invalid-feedback">{{ $message }}</div> @enderror</div>
</div>

<div class="card card-outline card-secondary mb-3">
<div class="card-header"><h3 class="card-title">Adresse detaillee</h3></div>
<div class="card-body">
    <div class="row">
        <div class="col-md-6 form-group"><label>Route / Avenue</label><input type="text" name="address_route" class="form-control @error('address_route') is-invalid @enderror" value="{{ old('address_route', optional($school)->address_route) }}" placeholder="Route de l'Aeroport Km 3">@error('address_route') <div class="invalid-feedback">{{ $message }}</div> @enderror</div>
        <div class="col-md-6 form-group"><label>Rue / Complement</label><input type="text" name="address_street" class="form-control @error('address_street') is-invalid @enderror" value="{{ old('address_street', optional($school)->address_street) }}" placeholder="Rue Abdelaziz Thaalbi">@error('address_street') <div class="invalid-feedback">{{ $message }}</div> @enderror</div>
    </div>
    <div class="row">
        <div class="col-md-4 form-group"><label>Code postal</label><input type="text" name="address_postal_code" class="form-control @error('address_postal_code') is-invalid @enderror" value="{{ old('address_postal_code', optional($school)->address_postal_code) }}" placeholder="3000">@error('address_postal_code') <div class="invalid-feedback">{{ $message }}</div> @enderror</div>
        <div class="col-md-4 form-group"><label>Ville</label><input type="text" name="address_city" class="form-control @error('address_city') is-invalid @enderror" value="{{ old('address_city', optional($school)->address_city ?? optional($school)->city) }}" placeholder="Sfax Ville">@error('address_city') <div class="invalid-feedback">{{ $message }}</div> @enderror</div>
        <div class="col-md-4 form-group"><label>Gouvernorat</label><input type="text" name="address_governorate" class="form-control @error('address_governorate') is-invalid @enderror" value="{{ old('address_governorate', optional($school)->address_governorate) }}" placeholder="Sfax">@error('address_governorate') <div class="invalid-feedback">{{ $message }}</div> @enderror</div>
    </div>
</div>
</div>

<div class="card card-outline card-info">
<div class="card-header d-flex justify-content-between align-items-center"><h3 class="card-title">Classes de cette ecole par annee scolaire</h3><button type="button" class="btn btn-sm btn-primary" data-add-school-class>Ajouter une classe</button></div>
<div class="card-body">
    <div data-school-classes-container>
        @foreach($classRows as $i => $row)
            <div class="row g-3 mb-3 border-bottom pb-3" data-school-class-row>
                <div class="col-md-3"><label>Annee scolaire</label><select name="classes[{{ $i }}][academic_year_id]" class="form-control" data-enhance-select="true"><option value="">Choisir...</option>@foreach($academicYears as $academicYear)<option value="{{ $academicYear->id }}" @selected((string) ($row['academic_year_id'] ?? null) === (string) $academicYear->id)>{{ $academicYear->label }}</option>@endforeach</select></div>
                <div class="col-md-3"><label>Classe</label><input type="text" name="classes[{{ $i }}][name]" class="form-control" value="{{ $row['name'] ?? '' }}"></div>
                <div class="col-md-2"><label>Niveau</label><input type="text" name="classes[{{ $i }}][level]" class="form-control" value="{{ $row['level'] ?? '' }}"></div>
                <div class="col-md-2"><label>Capacite</label><input type="number" min="1" name="classes[{{ $i }}][capacity]" class="form-control" value="{{ $row['capacity'] ?? '' }}"></div>
                <div class="col-md-1 d-flex align-items-end"><div class="form-check mb-3"><input type="hidden" name="classes[{{ $i }}][is_active]" value="0"><input class="form-check-input" type="checkbox" id="class_active_{{ $i }}" name="classes[{{ $i }}][is_active]" value="1" @checked(($row['is_active'] ?? false))><label class="form-check-label" for="class_active_{{ $i }}">Active</label></div></div>
                <div class="col-md-1 d-flex align-items-end justify-content-end"><button type="button" class="btn btn-outline-danger btn-sm" data-remove-school-class>Retirer</button></div>
            </div>
        @endforeach
    </div>
</div>
</div>

<template id="school-class-row-template">
    <div class="row g-3 mb-3 border-bottom pb-3" data-school-class-row>
        <div class="col-md-3"><label>Annee scolaire</label><select class="form-control" data-class-field="academic_year_id" data-enhance-select="true"><option value="">Choisir...</option>@foreach($academicYears as $academicYear)<option value="{{ $academicYear->id }}">{{ $academicYear->label }}</option>@endforeach</select></div>
        <div class="col-md-3"><label>Classe</label><input type="text" class="form-control" data-class-field="name"></div>
        <div class="col-md-2"><label>Niveau</label><input type="text" class="form-control" data-class-field="level"></div>
        <div class="col-md-2"><label>Capacite</label><input type="number" min="1" class="form-control" data-class-field="capacity"></div>
        <div class="col-md-1 d-flex align-items-end"><div class="form-check mb-3"><input type="hidden" value="0" data-class-hidden="is_active"><input class="form-check-input" type="checkbox" value="1" data-class-field="is_active"><label class="form-check-label">Active</label></div></div>
        <div class="col-md-1 d-flex align-items-end justify-content-end"><button type="button" class="btn btn-outline-danger btn-sm" data-remove-school-class>Retirer</button></div>
    </div>
</template>

@once
<script>
document.addEventListener('DOMContentLoaded', () => {
    const addButton = document.querySelector('[data-add-school-class]');
    const container = document.querySelector('[data-school-classes-container]');
    const template = document.getElementById('school-class-row-template');

    if (!addButton || !container || !template) {
        return;
    }

    const renumberRows = () => {
        Array.from(container.querySelectorAll('[data-school-class-row]')).forEach((row, index) => {
            row.querySelectorAll('[data-class-field]').forEach((field) => {
                const key = field.dataset.classField;
                field.name = `classes[${index}][${key}]`;
            });

            row.querySelectorAll('[data-class-hidden]').forEach((field) => {
                const key = field.dataset.classHidden;
                field.name = `classes[${index}][${key}]`;
            });
        });
    };

    addButton.addEventListener('click', () => {
        const fragment = template.content.cloneNode(true);
        container.appendChild(fragment);
        renumberRows();
    });

    container.addEventListener('click', (event) => {
        const trigger = event.target.closest('[data-remove-school-class]');

        if (!trigger) {
            return;
        }

        const row = trigger.closest('[data-school-class-row]');

        if (!row) {
            return;
        }

        row.remove();

        if (!container.querySelector('[data-school-class-row]')) {
            addButton.click();
        } else {
            renumberRows();
        }
    });

    renumberRows();
});
</script>
@endonce