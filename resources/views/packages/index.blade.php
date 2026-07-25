@extends('adminlte::page')

@section('title', 'Packages')

@section('content_header')
<div class="d-flex justify-content-between align-items-center">
    <h1 class="m-0">Packages</h1>
    <a href="{{ route('packages.create') }}" class="btn btn-primary">Nouveau package</a>
</div>
@stop

@section('content')
@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif

<div class="card package-catalog-card">
    <div class="card-body">
        <div class="package-catalog-toolbar mb-4">
            <div class="row g-3 align-items-end">
                <div class="col-12 col-lg-6">
                    <label for="package-search" class="form-label mb-1">Recherche</label>
                    <input
                        id="package-search"
                        type="search"
                        class="form-control"
                        placeholder="Rechercher un package..."
                    >
                </div>
                <div class="col-12 col-md-6 col-lg-3">
                    <label for="package-status-filter" class="form-label mb-1">Statut</label>
                    <select id="package-status-filter" class="form-select">
                        <option value="all">Tous</option>
                        <option value="active">Actifs</option>
                        <option value="inactive">Inactifs</option>
                    </select>
                </div>
                <div class="col-12 col-md-6 col-lg-3">
                    <label for="package-sort" class="form-label mb-1">Trier par</label>
                    <select id="package-sort" class="form-select">
                        <option value="status-name">Statut puis nom</option>
                        <option value="name">Nom (A-Z)</option>
                        <option value="price-desc">Total mensuel (desc)</option>
                        <option value="price-asc">Total mensuel (asc)</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="row g-4" id="packages-grid">
            @forelse($packages as $package)
                <div
                    class="col-12 col-md-6 col-xl-4 package-card-col"
                    data-package-card
                    data-name="{{ strtolower($package->nom) }}"
                    data-status="{{ $package->is_active ? 'active' : 'inactive' }}"
                    data-total="{{ (float) $package->total_mensuel }}"
                >
                    <article class="package-card h-100">
                        <div class="package-card-head">
                            <h3 class="package-card-title">{{ $package->nom }}</h3>
                            <span class="badge rounded-pill bg-{{ $package->is_active ? 'success' : 'secondary' }} package-card-status">
                                {{ $package->is_active ? 'Actif' : 'Inactif' }}
                            </span>
                        </div>

                        <ul class="package-card-prices list-unstyled mb-0">
                            <li>
                                <span>Scolarite</span>
                                <strong>{{ number_format((float) $package->frais_scolarite, 2, ',', ' ') }} TND</strong>
                            </li>
                            <li>
                                <span>Dejeuner</span>
                                <strong>{{ number_format((float) $package->frais_dejeuner, 2, ',', ' ') }} TND</strong>
                            </li>
                            <li>
                                <span>Activite</span>
                                <strong>{{ number_format((float) $package->frais_activite, 2, ',', ' ') }} TND</strong>
                            </li>
                            <li class="is-total">
                                <span>Total mensuel</span>
                                <strong>{{ number_format((float) $package->total_mensuel, 2, ',', ' ') }} TND</strong>
                            </li>
                        </ul>

                        <div class="package-card-actions mt-3">
                            @canany(['packages.view', 'packages.update', 'packages.delete'])
                                <div class="modern-action-group">
                                    @can('packages.view')
                                        <a href="{{ route('packages.show', $package) }}" class="modern-action-btn is-view"><i class="fa-solid fa-eye"></i><span>Voir</span></a>
                                    @endcan
                                    @can('packages.update')
                                        <a href="{{ route('packages.edit', $package) }}" class="modern-action-btn is-edit"><i class="fa-solid fa-pen"></i><span>Modifier</span></a>
                                    @endcan
                                    @can('packages.delete')
                                        <form method="POST" action="{{ route('packages.destroy', $package) }}" class="modern-inline-form" onsubmit="return confirm('Supprimer ce package ?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="modern-action-btn is-delete" type="submit"><i class="fa-solid fa-trash"></i><span>Supprimer</span></button>
                                        </form>
                                    @endcan
                                </div>
                            @else
                                <span class="text-muted">Aucune action disponible.</span>
                            @endcanany
                        </div>
                    </article>
                </div>
            @empty
                <div class="col-12">
                    <div class="alert alert-light border text-center mb-0">Aucun package.</div>
                </div>
            @endforelse
        </div>

        <p class="text-muted mt-3 mb-0" id="packages-empty-state" hidden>Aucun package ne correspond a votre recherche.</p>
    </div>
</div>
@stop

@section('js')
<script>
    (() => {
        const searchInput = document.getElementById('package-search');
        const statusFilter = document.getElementById('package-status-filter');
        const sortSelect = document.getElementById('package-sort');
        const grid = document.getElementById('packages-grid');
        const emptyState = document.getElementById('packages-empty-state');

        if (!searchInput || !statusFilter || !sortSelect || !grid || !emptyState) {
            return;
        }

        const cards = Array.from(grid.querySelectorAll('[data-package-card]'));

        const parseName = (el) => (el.dataset.name || '').trim();
        const parseStatus = (el) => (el.dataset.status || 'inactive').trim();
        const parseTotal = (el) => Number(el.dataset.total || 0);

        const applyFiltersAndSort = () => {
            const searchValue = searchInput.value.trim().toLowerCase();
            const statusValue = statusFilter.value;
            let visibleCount = 0;

            cards.forEach((card) => {
                const matchesSearch = parseName(card).includes(searchValue);
                const matchesStatus = statusValue === 'all' || parseStatus(card) === statusValue;
                const isVisible = matchesSearch && matchesStatus;

                card.classList.toggle('d-none', !isVisible);

                if (isVisible) {
                    visibleCount += 1;
                }
            });

            const sortedVisibleCards = cards
                .filter((card) => !card.classList.contains('d-none'))
                .sort((a, b) => {
                    const mode = sortSelect.value;

                    if (mode === 'name') {
                        return parseName(a).localeCompare(parseName(b), 'fr');
                    }

                    if (mode === 'price-desc') {
                        return parseTotal(b) - parseTotal(a);
                    }

                    if (mode === 'price-asc') {
                        return parseTotal(a) - parseTotal(b);
                    }

                    const statusRank = { active: 0, inactive: 1 };
                    const byStatus = (statusRank[parseStatus(a)] ?? 2) - (statusRank[parseStatus(b)] ?? 2);

                    if (byStatus !== 0) {
                        return byStatus;
                    }

                    return parseName(a).localeCompare(parseName(b), 'fr');
                });

            sortedVisibleCards.forEach((card) => grid.appendChild(card));
            emptyState.hidden = visibleCount > 0;
        };

        searchInput.addEventListener('input', applyFiltersAndSort);
        statusFilter.addEventListener('change', applyFiltersAndSort);
        sortSelect.addEventListener('change', applyFiltersAndSort);

        applyFiltersAndSort();
    })();
</script>
@stop