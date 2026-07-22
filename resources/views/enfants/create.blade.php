@extends('adminlte::page')

@section('title', 'Nouvel Enfant')

@section('content_header')
    <h1 class="m-0">Ajouter un enfant</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('enfants.store') }}" enctype="multipart/form-data">
                @csrf

                <div class="row">
                    <div class="col-md-6 form-group">
                        <label>Parent principal</label>
                        <select name="parent_id" class="form-control @error('parent_id') is-invalid @enderror" required>
                            <option value="">Choisir...</option>
                            @foreach($parents as $parent)
                                <option value="{{ $parent->id }}" @selected(old('parent_id') == $parent->id)>{{ $parent->nom }} {{ $parent->prenom }}</option>
                            @endforeach
                        </select>
                        @error('parent_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        <small class="form-text text-muted">Ce parent est le contact principal de l'enfant.</small>
                    </div>
                    <div class="col-md-6 form-group">
                        <label>Classe / Ecole / Annee scolaire</label>
                        <select name="school_class_id" class="form-control @error('school_class_id') is-invalid @enderror" data-enhance-select="true">
                            <option value="">Choisir...</option>
                            @foreach($schoolClasses as $schoolClass)
                                <option value="{{ $schoolClass->id }}" @selected((string) old('school_class_id') === (string) $schoolClass->id)>{{ $schoolClass->name }} - {{ $schoolClass->school?->name }} - {{ $schoolClass->academicYear?->label }}</option>
                            @endforeach
                        </select>
                        @error('school_class_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        <small class="form-text text-muted">{{ $activeAcademicYear ? 'Annee active : '.$activeAcademicYear->label : 'Aucune annee scolaire active definie.' }}</small>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 form-group">
                        <label>Nom</label>
                        <input type="text" name="nom" class="form-control @error('nom') is-invalid @enderror" value="{{ old('nom') }}" required>
                        @error('nom') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6 form-group">
                        <label>Prenom</label>
                        <input type="text" name="prenom" class="form-control @error('prenom') is-invalid @enderror" value="{{ old('prenom') }}" required>
                        @error('prenom') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 form-group">
                        <label>Date de naissance</label>
                        <input type="date" name="date_naissance" class="form-control @error('date_naissance') is-invalid @enderror" value="{{ old('date_naissance') }}" required>
                        @error('date_naissance') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4 form-group">
                        <label>Sexe</label>
                        <select name="sexe" class="form-control @error('sexe') is-invalid @enderror" required>
                            <option value="">Choisir...</option>
                            <option value="M" @selected(old('sexe') === 'M')>M</option>
                            <option value="F" @selected(old('sexe') === 'F')>F</option>
                        </select>
                        @error('sexe') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4 form-group">
                        <label>Photo</label>
                        <input type="file" name="photo" class="form-control-file @error('photo') is-invalid @enderror">
                        @error('photo') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="form-group">
                    <div class="form-check mb-2">
                        <input type="hidden" name="has_allergie" value="0">
                        <input type="checkbox" name="has_allergie" value="1" class="form-check-input" id="has_allergie_create" @checked(old('has_allergie'))>
                        <label class="form-check-label" for="has_allergie_create">Alergie</label>
                    </div>
                    <div class="row g-2 mb-2">
                        @foreach($allergieOptions as $allergieOption)
                            <div class="col-lg-4 col-md-6">
                                <div class="form-check">
                                    <input
                                        type="checkbox"
                                        name="allergie_options[]"
                                        value="{{ $allergieOption }}"
                                        class="form-check-input"
                                        id="allergie_create_{{ \Illuminate\Support\Str::slug($allergieOption, '_') }}"
                                        @checked(collect(old('allergie_options', []))->contains($allergieOption))
                                    >
                                    <label class="form-check-label" for="allergie_create_{{ \Illuminate\Support\Str::slug($allergieOption, '_') }}">{{ $allergieOption }}</label>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <textarea name="allergies" rows="2" class="form-control @error('allergies') is-invalid @enderror" placeholder="Preciser l'allergie si necessaire...">{{ old('allergies') }}</textarea>
                    <small class="form-text text-muted d-block mt-2">Selectionnez les allergies connues puis detaillez si necessaire dans le champ ci-dessus.</small>
                    @error('allergie_options') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    @error('allergie_options.*') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    @error('allergies') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="form-group">
                    <label>Observations</label>
                    <textarea name="observations" rows="2" class="form-control @error('observations') is-invalid @enderror">{{ old('observations') }}</textarea>
                    @error('observations') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="card card-outline card-info">
                    <div class="card-header">
                        <h3 class="card-title">Parents rattaches (liens de relation)</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @foreach($relationOptions as $relationKey => $relationLabel)
                                <div class="col-md-6 form-group">
                                    <label>{{ $relationLabel }}</label>
                                    <select name="relations[{{ $relationKey }}]" class="form-control @error('relations.' . $relationKey) is-invalid @enderror">
                                        <option value="">Aucun</option>
                                        @foreach($parents as $parent)
                                            <option value="{{ $parent->id }}" @selected(old('relations.' . $relationKey) == $parent->id)>
                                                {{ $parent->nom }} {{ $parent->prenom }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('relations.' . $relationKey) <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <button class="btn btn-primary">Enregistrer</button>
                <a href="{{ route('enfants.index') }}" class="btn btn-secondary">Annuler</a>
            </form>
        </div>
    </div>
@stop
