@extends('adminlte::page')

@section('title', 'Detail Parent')

@section('content_header')
    <h1 class="m-0">Detail parent</h1>
@stop

@section('content')
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif
    @if (session('temporary_password'))
        <div class="alert alert-info">Mot de passe temporaire du compte parent: <strong>{{ session('temporary_password') }}</strong></div>
    @endif

    <div class="card">
        <div class="card-body">
            <dl class="row mb-0">
                <dt class="col-sm-3">Nom complet</dt>
                <dd class="col-sm-9">{{ $parent->nom }} {{ $parent->prenom }}</dd>

                <dt class="col-sm-3">N CIN</dt>
                <dd class="col-sm-9">{{ $parent->numero_cin ?: '-' }}</dd>

                <dt class="col-sm-3">Date de delivrance</dt>
                <dd class="col-sm-9">{{ optional($parent->date_delivrance_cin)->format('d/m/Y') ?: '-' }}</dd>

                <dt class="col-sm-3">Date de naissance</dt>
                <dd class="col-sm-9">{{ optional($parent->date_naissance)->format('d/m/Y') ?: '-' }}</dd>

                <dt class="col-sm-3">Sexe</dt>
                <dd class="col-sm-9">{{ $parent->sexe ?: '-' }}</dd>

                <dt class="col-sm-3">Telephone</dt>
                <dd class="col-sm-9">{{ $parent->telephone }}</dd>

                <dt class="col-sm-3">Email</dt>
                <dd class="col-sm-9">{{ $parent->email ?: '-' }}</dd>

                <dt class="col-sm-3">Adresse</dt>
                <dd class="col-sm-9">{{ $parent->adresse ?: '-' }}</dd>

                <dt class="col-sm-3">Profession</dt>
                <dd class="col-sm-9">{{ $parent->profession ?: '-' }}</dd>

                <dt class="col-sm-3">Contact urgence</dt>
                <dd class="col-sm-9">{{ $parent->contact_urgence ?: '-' }}</dd>

                <dt class="col-sm-3">Nombre d'enfants</dt>
                <dd class="col-sm-9">{{ $parent->enfants_count }}</dd>

                <dt class="col-sm-3">Documents CIN</dt>
                <dd class="col-sm-9">
                    @if($parent->cin_recto)
                        <a href="{{ asset('storage/' . $parent->cin_recto) }}" target="_blank" rel="noopener">Recto</a>
                    @else
                        <span class="text-danger">Recto manquant</span>
                    @endif
                    |
                    @if($parent->cin_verso)
                        <a href="{{ asset('storage/' . $parent->cin_verso) }}" target="_blank" rel="noopener">Verso</a>
                    @else
                        <span class="text-danger">Verso manquant</span>
                    @endif
                </dd>

                <dt class="col-sm-3">Statut profil</dt>
                <dd class="col-sm-9">
                    @if($parent->cin_recto && $parent->cin_verso)
                        <span class="badge badge-success">Valide</span>
                    @else
                        <span class="badge badge-danger">Non valide (documents manquants)</span>
                    @endif
                </dd>

                <dt class="col-sm-3">Compte utilisateur parent</dt>
                <dd class="col-sm-9">
                    @if($parent->user)
                        <div>{{ $parent->user->email }}</div>
                        <small class="text-muted">Roles: {{ $parent->user->getRoleNames()->join(', ') ?: '-' }}</small>
                    @else
                        <span class="text-warning">Aucun compte utilisateur associe</span>

                        @can('users.manage')
                            <div class="mt-2">
                                @if($parent->email)
                                    <form method="POST" action="{{ route('parents.create-user', $parent) }}" class="d-inline" onsubmit="return confirm('Creer et associer un compte utilisateur pour ce parent ?')">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-primary">Creer un user parent</button>
                                    </form>
                                @else
                                    <small class="text-danger d-block">Ajoutez un email au parent pour pouvoir generer son compte utilisateur.</small>
                                @endif
                            </div>
                        @endcan
                    @endif
                </dd>
            </dl>
        </div>
        <div class="card-footer">
            <a href="{{ route('parents.edit', $parent) }}" class="btn btn-warning">Modifier</a>
            <a href="{{ route('parents.index') }}" class="btn btn-secondary">Retour</a>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Enfants rattaches</h3>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped table-bordered mb-0">
                    <thead>
                    <tr>
                        <th>Lien</th>
                        <th>Nom</th>
                        <th>Prenom</th>
                        <th>Date naissance</th>
                        <th>Classe</th>
                        <th width="120">Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($linkedEnfants as $enfant)
                        @php
                            $relationLabels = collect();

                            if ((int) $enfant->parent_id === (int) $parent->id) {
                                $relationLabels->push('Parent principal');
                            }

                            foreach ($enfant->familyRelations as $familyRelation) {
                                if ($familyRelation->relation) {
                                    $relationLabels->push($familyRelation->relation);
                                }
                            }

                            $relationText = $relationLabels->filter()->unique()->join(', ');
                        @endphp
                        <tr>
                            <td>{{ $relationText ?: '-' }}</td>
                            <td>{{ $enfant->nom }}</td>
                            <td>{{ $enfant->prenom }}</td>
                            <td>{{ optional($enfant->date_naissance)->format('d/m/Y') }}</td>
                            <td>{{ $enfant->classe ?: '-' }}</td>
                            <td>
                                <a href="{{ route('enfants.show', $enfant) }}" class="btn btn-sm btn-info">Voir</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">Aucun enfant rattache a ce parent.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@stop
