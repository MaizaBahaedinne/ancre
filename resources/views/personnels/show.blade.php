@extends('adminlte::page')
@section('title', 'Detail Personnel')
@section('content_header')<h1 class="m-0">Detail employe</h1>@stop
@section('content')
@if(session('success'))
<div class="alert alert-success">{{ session('success') }}</div>
@endif

@if(session('error'))
<div class="alert alert-danger">{{ session('error') }}</div>
@endif

@if(session('temporary_password'))
<div class="alert alert-warning">Mot de passe temporaire du compte cree : <strong>{{ session('temporary_password') }}</strong></div>
@endif

<div class="child-profile-shell">
<section class="child-profile-hero">
<div class="child-profile-hero-grid">
<div class="child-profile-avatar-wrap">
	@if($personnel->photo)
		<img src="{{ asset('storage/'.$personnel->photo) }}" alt="Photo de {{ $personnel->prenom }} {{ $personnel->nom }}" class="child-profile-avatar">
	@else
		<div class="child-profile-avatar child-profile-avatar-placeholder">{{ strtoupper(substr($personnel->prenom ?: $personnel->nom, 0, 1)) }}</div>
	@endif
</div>
<div>
	<p class="child-profile-kicker">Profil personnel</p>
	<h2 class="child-profile-name">{{ $personnel->prenom }} {{ $personnel->nom }}</h2>
	<div class="child-profile-meta">
		<span><i class="fa-solid fa-briefcase"></i>{{ $personnel->fonction ?: '-' }}</span>
		<span><i class="fa-solid fa-building-user"></i>{{ $personnel->departement ?: '-' }}</span>
		<span><i class="fa-solid fa-phone"></i>{{ $personnel->telephone ?: '-' }}</span>
	</div>
	<div class="child-profile-tags">
		<span class="child-profile-chip">{{ $personnel->sexe === 'M' ? 'Masculin' : ($personnel->sexe === 'F' ? 'Feminin' : '-') }}</span>
		<span class="child-profile-chip">{{ $personnel->annees_experience ?? 0 }} an(s) d'experience</span>
		<span class="child-profile-chip {{ $personnel->user ? 'is-safe' : 'is-alert' }}">{{ $personnel->user ? 'Compte utilisateur actif' : 'Aucun compte utilisateur' }}</span>
	</div>
	<div class="child-profile-actions">
		<a href="{{ route('personnels.edit', $personnel) }}" class="btn btn-warning">Modifier</a>
		<a href="{{ route('personnels.index') }}" class="btn btn-secondary">Retour</a>
	</div>
</div>
</div>
</section>

<div class="row g-4 mt-1">
<div class="col-lg-4">
	<div class="child-profile-side-card card"><div class="card-body">
		<ul class="child-profile-facts list-unstyled mb-0">
			<li><i class="fa-solid fa-envelope"></i><span>Email</span><strong>{{ $personnel->email ?: '-' }}</strong></li>
			<li><i class="fa-solid fa-id-card"></i><span>CIN</span><strong>{{ $personnel->numero_cin ?: '-' }}</strong></li>
			<li><i class="fa-solid fa-calendar-days"></i><span>Date embauche</span><strong>{{ optional($personnel->date_embauche)->format('d/m/Y') ?: '-' }}</strong></li>
			<li><i class="fa-solid fa-user-tie"></i><span>Manager</span><strong>{{ $personnel->manager?->nom }} {{ $personnel->manager?->prenom ?: '-' }}</strong></li>
			<li><i class="fa-solid fa-user-shield"></i><span>Compte</span><strong>{{ $personnel->user?->email ?: '-' }}</strong></li>
			<li><i class="fa-solid fa-school"></i><span>Ecole</span><strong>{{ $personnel->school?->name ?: '-' }}</strong></li>
			<li><i class="fa-solid fa-graduation-cap"></i><span>Classe</span><strong>{{ $personnel->schoolClass?->name ?: '-' }}</strong></li>
		</ul>
	</div></div>
</div>
<div class="col-lg-8">
	<div class="card mb-4"><div class="card-body child-profile-panel">
		<h4>Informations personnelles</h4>
		<div class="row">
			<div class="col-md-6"><p><strong>Date de naissance:</strong> {{ optional($personnel->date_naissance)->format('d/m/Y') ?: '-' }}</p></div>
			<div class="col-md-6"><p><strong>Numero CNSS:</strong> {{ $personnel->numero_cnss ?: '-' }}</p></div>
			<div class="col-md-6"><p><strong>Niveau d'etude:</strong> {{ $personnel->niveau_etude ?: '-' }}</p></div>
			<div class="col-md-6"><p><strong>Domaine d'etude:</strong> {{ $personnel->domaine_etude ?: '-' }}</p></div>
			<div class="col-md-6"><p><strong>Date delivrance CIN:</strong> {{ optional($personnel->date_delivrance_cin)->format('d/m/Y') ?: '-' }}</p></div>
			<div class="col-md-6"><p><strong>Lieu delivrance CIN:</strong> {{ $personnel->lieu_delivrance_cin ?: '-' }}</p></div>
		</div>
	</div></div>

	<div class="card mb-4"><div class="card-body child-profile-panel">
		<h4>Adresse</h4>
		<div class="row">
			<div class="col-md-6"><p><strong>N rue:</strong> {{ $personnel->adresse_rue ?: '-' }}</p></div>
			<div class="col-md-6"><p><strong>Ville:</strong> {{ $personnel->adresse_ville ?: '-' }}</p></div>
			<div class="col-md-6"><p><strong>Gouvernorat:</strong> {{ $personnel->adresse_gouvernorat ?: '-' }}</p></div>
			<div class="col-md-6"><p><strong>Code postale:</strong> {{ $personnel->adresse_code_postal ?: '-' }}</p></div>
		</div>
	</div></div>

	@can('users.manage')
		@if(! $personnel->user)
		<div class="card"><div class="card-body child-profile-panel">
			<h4>Creer un compte utilisateur</h4>
			<p class="child-profile-note">Ce personnel n'a pas encore de compte. Choisissez le type de compte a creer.</p>
			<form method="POST" action="{{ route('personnels.create-user', $personnel) }}" class="row g-3 align-items-end">
				@csrf
				<div class="col-md-6">
					<label for="user_role" class="form-label">Role du compte</label>
					<select id="user_role" name="user_role" class="form-control @error('user_role') is-invalid @enderror" required data-enhance-select="true">
						<option value="">Choisir...</option>
						@foreach($roles as $role)
						<option value="{{ $role->name }}" @selected(old('user_role') === $role->name)>{{ $role->name }}</option>
						@endforeach
					</select>
					@error('user_role') <div class="invalid-feedback">{{ $message }}</div> @enderror
				</div>
				<div class="col-md-6">
					<button type="submit" class="btn btn-primary">Creer un compte</button>
				</div>
			</form>
		</div></div>
		@endif
	@endcan
</div>
</div>
</div>
@stop
