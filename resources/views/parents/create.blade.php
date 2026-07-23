@extends('adminlte::page')

@section('title', 'Nouveau Parent')

@section('content_header')
    <h1 class="m-0">Ajouter un parent</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('parents.store') }}" enctype="multipart/form-data" id="parent-create-form">
                @csrf

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
                    <div class="col-md-6 form-group">
                        <label>N Carte d'identite nationale</label>
                        <input type="text" name="numero_cin" class="form-control @error('numero_cin') is-invalid @enderror" value="{{ old('numero_cin') }}" required>
                        @error('numero_cin') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6 form-group">
                        <label>Date de delivrance</label>
                        <input type="date" name="date_delivrance_cin" class="form-control @error('date_delivrance_cin') is-invalid @enderror" value="{{ old('date_delivrance_cin') }}" required>
                        @error('date_delivrance_cin') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 form-group">
                        <label>Date de naissance</label>
                        <input type="date" name="date_naissance" class="form-control @error('date_naissance') is-invalid @enderror" value="{{ old('date_naissance') }}" required>
                        @error('date_naissance') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6 form-group">
                        <label>Sexe</label>
                        <select name="sexe" class="form-control @error('sexe') is-invalid @enderror" required>
                            <option value="">Choisir...</option>
                            <option value="M" @selected(old('sexe') === 'M')>M</option>
                            <option value="F" @selected(old('sexe') === 'F')>F</option>
                        </select>
                        @error('sexe') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 form-group">
                        <label>Telephone</label>
                        <input type="text" name="telephone" class="form-control @error('telephone') is-invalid @enderror" value="{{ old('telephone') }}" required>
                        @error('telephone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6 form-group">
                        <label>Email</label>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}">
                        @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 form-group">
                        <label>Profession</label>
                        <input type="text" name="profession" class="form-control @error('profession') is-invalid @enderror" value="{{ old('profession') }}">
                        @error('profession') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6 form-group">
                        <label>Contact urgence</label>
                        <input type="text" name="contact_urgence" class="form-control @error('contact_urgence') is-invalid @enderror" value="{{ old('contact_urgence') }}">
                        @error('contact_urgence') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 form-group">
                        <label>Rue</label>
                        <input type="text" name="adresse_rue" class="form-control @error('adresse_rue') is-invalid @enderror" value="{{ old('adresse_rue') }}">
                        @error('adresse_rue') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4 form-group">
                        <label>Ville</label>
                        <input type="text" name="adresse_ville" class="form-control @error('adresse_ville') is-invalid @enderror" value="{{ old('adresse_ville') }}">
                        @error('adresse_ville') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4 form-group">
                        <label>Gouvernorat</label>
                        <input type="text" name="adresse_gouvernorat" class="form-control @error('adresse_gouvernorat') is-invalid @enderror" value="{{ old('adresse_gouvernorat') }}">
                        @error('adresse_gouvernorat') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 form-group">
                        <label>CIN Recto (image ou PDF)</label>
                        <input type="file" name="cin_recto" id="cin-recto-file" class="form-control-file @error('cin_recto') is-invalid @enderror" accept="image/*,application/pdf">
                        @error('cin_recto') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6 form-group">
                        <label>CIN Verso (image ou PDF)</label>
                        <input type="file" name="cin_verso" id="cin-verso-file" class="form-control-file @error('cin_verso') is-invalid @enderror" accept="image/*,application/pdf">
                        @error('cin_verso') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    </div>
                </div>
                <small class="text-muted d-block mb-3">Le profil parent ne peut pas etre valide sans ces documents.</small>

                <button class="btn btn-primary" id="parent-submit-btn">Enregistrer</button>
                <a href="{{ route('parents.index') }}" class="btn btn-secondary">Annuler</a>
            </form>
        </div>
    </div>
@stop
