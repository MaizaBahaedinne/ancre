@extends('adminlte::page')

@section('title', 'Personnel')

@section('content_header')
<div class="d-flex justify-content-between align-items-center">
    <h1 class="m-0">Personnel</h1>
    <a href="{{ route('personnels.create') }}" class="btn btn-primary">Ajouter employe</a>
</div>
@stop

@section('content')
@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="row mb-4">
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card border-0 shadow-sm h-100"><div class="card-body"><div class="text-muted small">Personnel</div><div class="display-6 fw-semibold">{{ $stats['total'] ?? 0 }}</div></div></div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card border-0 shadow-sm h-100"><div class="card-body"><div class="text-muted small">Avec email</div><div class="display-6 fw-semibold">{{ $stats['with_email'] ?? 0 }}</div></div></div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card border-0 shadow-sm h-100"><div class="card-body"><div class="text-muted small">Par sexe</div><div class="small mt-2">@forelse(($stats['by_gender'] ?? collect()) as $item)<div class="d-flex justify-content-between"><span>{{ $item->sexe === 'M' ? 'Masculin' : 'Feminin' }}</span><strong>{{ $item->aggregate }}</strong></div>@empty<div class="text-muted">-</div>@endforelse</div></div></div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card border-0 shadow-sm h-100"><div class="card-body"><div class="text-muted small">Par departement</div><div class="small mt-2">@forelse(($stats['by_department'] ?? collect())->take(4) as $item)<div class="d-flex justify-content-between"><span>{{ $item->departement }}</span><strong>{{ $item->aggregate }}</strong></div>@empty<div class="text-muted">-</div>@endforelse</div></div></div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="text-muted small mb-2">Nombre par fonction</div>
                <div class="row g-3">
                    @forelse(($stats['by_function'] ?? collect())->take(8) as $item)
                        <div class="col-lg-3 col-md-4 col-sm-6">
                            <div class="border rounded-4 p-3 h-100 bg-light-subtle">
                                <div class="small text-muted">{{ $item->fonction }}</div>
                                <div class="h4 mb-0 fw-semibold">{{ $item->aggregate }}</div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12 text-muted">Aucune repartition disponible.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body modern-table-card">
        <div class="table-responsive">
            <table class="table table-striped table-bordered js-data-table nowrap">
                <thead>
                <tr>
                    <th>Nom</th>
                    <th>Prenom</th>
                    <th>Fonction</th>
                    <th>Telephone</th>
                    <th>Email</th>
                    <th>Compte utilisateur</th>
                    <th width="210" class="no-sort">Actions</th>
                </tr>
                </thead>
                <tbody>
                @forelse($personnels as $personnel)
                    <tr>
                        <td>{{ $personnel->nom }}</td>
                        <td>{{ $personnel->prenom }}</td>
                        <td>{{ $personnel->fonction }}</td>
                        <td>{{ $personnel->telephone }}</td>
                        <td>{{ $personnel->email ?: '-' }}</td>
                        <td>
                            @if($personnel->user)
                                <span class="badge bg-success">Oui</span>
                                <div class="small text-muted mt-1">{{ $personnel->user->email }}</div>
                            @else
                                <span class="badge bg-secondary">Non</span>
                            @endif
                        </td>
                        <td>
                            @canany(['personnels.view', 'personnels.update', 'personnels.delete'])
                                <div class="modern-action-group">
                                    @can('personnels.view')
                                        <a href="{{ route('personnels.show', $personnel) }}" class="modern-action-btn is-view"><i class="fa-solid fa-eye"></i><span>Voir</span></a>
                                    @endcan
                                    @can('personnels.update')
                                        <a href="{{ route('personnels.edit', $personnel) }}" class="modern-action-btn is-edit"><i class="fa-solid fa-pen"></i><span>Modifier</span></a>
                                    @endcan
                                    @can('personnels.delete')
                                        <form method="POST" action="{{ route('personnels.destroy', $personnel) }}" class="modern-inline-form" onsubmit="return confirm('Supprimer cet employe ?')">
                                        @csrf
                                        @method('DELETE')
                                            <button class="modern-action-btn is-delete" type="submit"><i class="fa-solid fa-trash"></i><span>Supprimer</span></button>
                                        </form>
                                    @endcan
                                </div>
                            @else
                                <span class="text-muted">-</span>
                            @endcanany
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center">Aucun employe.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@stop
