<div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
    <h1 class="m-0">Gestion du site vitrine</h1>
    <a href="{{ route('vitrine.home') }}" class="btn btn-outline-primary" target="_blank" rel="noopener">Voir la vitrine</a>
</div>

<div class="card mb-4">
    <div class="card-body py-2">
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('admin.vitrine.settings') }}" class="btn btn-sm {{ request()->routeIs('admin.vitrine.settings') ? 'btn-primary' : 'btn-outline-primary' }}">Parametres</a>
            <a href="{{ route('admin.vitrine.pages') }}" class="btn btn-sm {{ request()->routeIs('admin.vitrine.pages') ? 'btn-primary' : 'btn-outline-primary' }}">Pages</a>
            <a href="{{ route('admin.vitrine.services') }}" class="btn btn-sm {{ request()->routeIs('admin.vitrine.services') ? 'btn-primary' : 'btn-outline-primary' }}">Services</a>
            <a href="{{ route('admin.vitrine.schedules') }}" class="btn btn-sm {{ request()->routeIs('admin.vitrine.schedules') ? 'btn-primary' : 'btn-outline-primary' }}">Horaires</a>
            <a href="{{ route('admin.vitrine.social-posts') }}" class="btn btn-sm {{ request()->routeIs('admin.vitrine.social-posts') ? 'btn-primary' : 'btn-outline-primary' }}">Reseaux sociaux</a>
            <a href="{{ route('admin.vitrine.testimonials') }}" class="btn btn-sm {{ request()->routeIs('admin.vitrine.testimonials') ? 'btn-primary' : 'btn-outline-primary' }}">Temoignages</a>
            <a href="{{ route('admin.vitrine.leads') }}" class="btn btn-sm {{ request()->routeIs('admin.vitrine.leads') ? 'btn-primary' : 'btn-outline-primary' }}">Leads</a>
        </div>
    </div>
</div>
