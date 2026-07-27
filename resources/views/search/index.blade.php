@extends('adminlte::page')

@section('title', 'Résultats de recherche')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h1 class="h3 mb-3">
                        <i class="fa-solid fa-magnifying-glass"></i>
                        Résultats de recherche
                    </h1>
                    
                    <form method="GET" action="{{ route('search.index') }}" class="mb-4">
                        <div class="input-group input-group-lg">
                            <input type="text" name="q" class="form-control" placeholder="Rechercher..." value="{{ $query }}">
                            <button class="btn btn-outline-primary" type="submit">
                                <i class="fa-solid fa-search"></i> Rechercher
                            </button>
                        </div>
                        <small class="text-muted d-block mt-2">
                            Recherchez par nom, email, numéro de téléphone...
                        </small>
                    </form>

                    @if($query)
                        @if(count($results) > 0)
                            <div class="search-results">
                                <p class="text-muted mb-3">
                                    <strong>{{ count($results) }}</strong> résultat(s) trouvé(s) pour "<strong>{{ $query }}</strong>"
                                </p>

                                <div class="list-group">
                                    @foreach($results as $result)
                                        <a href="{{ $result['url'] }}" class="list-group-item list-group-item-action">
                                            <div class="d-flex w-100 justify-content-between align-items-start">
                                                <div>
                                                    <div class="mb-1">
                                                        <i class="fa-solid {{ $result['icon'] }} text-primary me-2"></i>
                                                        <strong>{{ $result['label'] }}</strong>
                                                        <span class="badge bg-light text-dark ms-2">
                                                            {{ ucfirst($result['type']) }}
                                                        </span>
                                                    </div>
                                                    @if($result['subtitle'])
                                                        <small class="text-muted">{{ $result['subtitle'] }}</small>
                                                    @endif
                                                </div>
                                                <i class="fa-solid fa-arrow-right text-muted"></i>
                                            </div>
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @else
                            <div class="alert alert-info">
                                <i class="fa-solid fa-circle-info me-2"></i>
                                Aucun résultat trouvé pour "<strong>{{ $query }}</strong>"
                            </div>
                        @endif
                    @else
                        <div class="alert alert-warning">
                            <i class="fa-solid fa-triangle-exclamation me-2"></i>
                            Veuillez entrer au moins 2 caractères pour rechercher
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .search-results .list-group-item {
        border: 1px solid #e5e7eb;
        padding: 1rem;
    }

    .search-results .list-group-item:hover {
        background-color: #f8f9fa;
        border-color: var(--brand-gold);
    }

    .search-results .list-group-item.active {
        background-color: var(--brand-navy);
        border-color: var(--brand-navy);
    }
</style>
@endsection
