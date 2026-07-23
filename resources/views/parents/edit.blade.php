@extends('adminlte::page')

@section('title', 'Modifier Parent')

@section('content_header')
    <h1 class="m-0">Modifier parent</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('parents.update', $parent) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="row">
                    <div class="col-md-6 form-group">
                        <label>Nom</label>
                        <input type="text" name="nom" class="form-control @error('nom') is-invalid @enderror" value="{{ old('nom', $parent->nom) }}" required>
                        @error('nom') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6 form-group">
                        <label>Prenom</label>
                        <input type="text" name="prenom" class="form-control @error('prenom') is-invalid @enderror" value="{{ old('prenom', $parent->prenom) }}" required>
                        @error('prenom') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 form-group">
                        <label>N Carte d'identite nationale</label>
                        <input type="text" name="numero_cin" class="form-control @error('numero_cin') is-invalid @enderror" value="{{ old('numero_cin', $parent->numero_cin) }}" required>
                        @error('numero_cin') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6 form-group">
                        <label>Date de delivrance</label>
                        <input type="date" name="date_delivrance_cin" class="form-control @error('date_delivrance_cin') is-invalid @enderror" value="{{ old('date_delivrance_cin', optional($parent->date_delivrance_cin)->format('Y-m-d')) }}" required>
                        @error('date_delivrance_cin') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 form-group">
                        <label>Date de naissance</label>
                        <input type="date" name="date_naissance" class="form-control @error('date_naissance') is-invalid @enderror" value="{{ old('date_naissance', optional($parent->date_naissance)->format('Y-m-d')) }}" required>
                        @error('date_naissance') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6 form-group">
                        <label>Sexe</label>
                        <select name="sexe" class="form-control @error('sexe') is-invalid @enderror" required>
                            <option value="M" @selected(old('sexe', $parent->sexe) === 'M')>M</option>
                            <option value="F" @selected(old('sexe', $parent->sexe) === 'F')>F</option>
                        </select>
                        @error('sexe') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 form-group">
                        <label>Telephone</label>
                        <input type="text" name="telephone" class="form-control @error('telephone') is-invalid @enderror" value="{{ old('telephone', $parent->telephone) }}" required>
                        @error('telephone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6 form-group">
                        <label>Email</label>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $parent->email) }}">
                        @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 form-group">
                        <label>Profession</label>
                        <input type="text" name="profession" class="form-control @error('profession') is-invalid @enderror" value="{{ old('profession', $parent->profession) }}">
                        @error('profession') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6 form-group">
                        <label>Contact urgence</label>
                        <input type="text" name="contact_urgence" class="form-control @error('contact_urgence') is-invalid @enderror" value="{{ old('contact_urgence', $parent->contact_urgence) }}">
                        @error('contact_urgence') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 form-group">
                        <label>Rue</label>
                        <input type="text" name="adresse_rue" class="form-control @error('adresse_rue') is-invalid @enderror" value="{{ old('adresse_rue', $parent->adresse_rue) }}">
                        @error('adresse_rue') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4 form-group">
                        <label>Ville</label>
                        <input type="text" name="adresse_ville" class="form-control @error('adresse_ville') is-invalid @enderror" value="{{ old('adresse_ville', $parent->adresse_ville) }}">
                        @error('adresse_ville') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4 form-group">
                        <label>Gouvernorat</label>
                        <input type="text" name="adresse_gouvernorat" class="form-control @error('adresse_gouvernorat') is-invalid @enderror" value="{{ old('adresse_gouvernorat', $parent->adresse_gouvernorat) }}">
                        @error('adresse_gouvernorat') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="card card-outline card-info">
                    <div class="card-header">
                        <h3 class="card-title">Documents d'identite</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label>CIN Recto (image ou PDF)</label>
                                <input type="file" name="cin_recto" class="form-control-file @error('cin_recto') is-invalid @enderror" {{ $parent->cin_recto ? '' : 'required' }}>
                                @error('cin_recto') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                @if($parent->cin_recto)
                                    <div class="mt-2">
                                        <a href="{{ asset('storage/' . $parent->cin_recto) }}" target="_blank" rel="noopener">Voir document actuel</a>
                                    </div>
                                @endif
                            </div>
                            <div class="col-md-6 form-group">
                                <label>CIN Verso (image ou PDF)</label>
                                <input type="file" name="cin_verso" class="form-control-file @error('cin_verso') is-invalid @enderror" {{ $parent->cin_verso ? '' : 'required' }}>
                                @error('cin_verso') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                @if($parent->cin_verso)
                                    <div class="mt-2">
                                        <a href="{{ asset('storage/' . $parent->cin_verso) }}" target="_blank" rel="noopener">Voir document actuel</a>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <small class="text-muted">Le profil parent ne peut pas etre valide sans CIN recto et verso.</small>
                    </div>
                </div>

                <button class="btn btn-primary">Mettre a jour</button>
                <a href="{{ route('parents.index') }}" class="btn btn-secondary">Annuler</a>
            </form>
        </div>
    </div>
@stop
