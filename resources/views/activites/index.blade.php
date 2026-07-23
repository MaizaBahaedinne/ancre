@extends('adminlte::page')

@section('title', ($isRestrictedEducator ?? false) ? 'Mes Activites' : 'Activites')

@section('css')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.css">
@endsection

@section('content_header')
<div class="d-flex justify-content-between align-items-center">
    <h1 class="m-0">{{ ($isRestrictedEducator ?? false) ? 'Mes activites' : 'Activites' }}</h1>
    <a href="{{ route('activites.create') }}" class="btn btn-primary">Ajouter activite</a>
</div>
@stop

@section('content')
@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

@if(($isRestrictedEducator ?? false) && ! $educatorPersonnel)
    <div class="alert alert-warning">Ce compte educateur n'est lie a aucune fiche personnel. Associez d'abord ce user a un personnel pour afficher ses activites.</div>
@endif

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3 class="card-title mb-0">{{ ($isRestrictedEducator ?? false) ? 'Calendrier de mes activites' : 'Calendrier des activites' }}</h3>
        <span class="text-muted small">Vue mensuelle et hebdomadaire</span>
    </div>
    <div class="card-body">
        <div id="activites-calendar" class="modern-activities-calendar"></div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title mb-0">{{ ($isRestrictedEducator ?? false) ? 'Liste de mes activites' : 'Liste des activites' }}</h3>
    </div>
    <div class="card-body modern-table-card">
        <div class="table-responsive">
            <table class="table table-striped table-bordered js-data-table nowrap">
                <thead>
                    <tr>
                        <th>Titre</th>
                        <th>Date</th>
                        <th>Heure</th>
                        <th>Salle</th>
                        <th>Responsable</th>
                        <th width="210" class="no-sort">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($activites as $activite)
                        <tr>
                            <td>{{ $activite->titre }}</td>
                            <td>{{ optional($activite->date)->format('d/m/Y') }}</td>
                            <td>{{ $activite->heure ?: '-' }}</td>
                            <td>{{ $activite->salle?->nom ?: '-' }}</td>
                            <td>{{ $activite->responsable }}</td>
                            <td>
                                @canany(['activities.view', 'activities.update', 'activities.delete'])
                                    <div class="modern-action-group">
                                        @can('activities.view')
                                            <a href="{{ route('activites.show', $activite) }}" class="modern-action-btn is-view"><i class="fa-solid fa-eye"></i><span>Voir</span></a>
                                        @endcan
                                        @can('activities.update')
                                            <a href="{{ route('activites.edit', $activite) }}" class="modern-action-btn is-edit"><i class="fa-solid fa-pen"></i><span>Modifier</span></a>
                                        @endcan
                                        @can('activities.delete')
                                            <form method="POST" action="{{ route('activites.destroy', $activite) }}" class="modern-inline-form" onsubmit="return confirm('Supprimer cette activite ?')">
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
                        <tr><td colspan="6" class="text-center">Aucune activite.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@stop

@section('js')
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const calendarElement = document.getElementById('activites-calendar');

        if (!calendarElement || !window.FullCalendar) {
            return;
        }

        const events = @json($calendarEvents);

        const calendar = new FullCalendar.Calendar(calendarElement, {
            initialView: 'dayGridMonth',
            locale: 'fr',
            firstDay: 1,
            height: 'auto',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,listMonth',
            },
            buttonText: {
                today: 'Aujourd\'hui',
                month: 'Mois',
                week: 'Semaine',
                list: 'Liste',
            },
            events,
            eventTimeFormat: {
                hour: '2-digit',
                minute: '2-digit',
                meridiem: false,
            },
            eventDidMount: (info) => {
                const responsable = info.event.extendedProps.responsable;
                const heure = info.event.extendedProps.heure;
                const salle = info.event.extendedProps.salle;
                const parts = [
                    responsable ? `Responsable: ${responsable}` : null,
                    salle ? `Salle: ${salle}` : null,
                    heure ? `Heure: ${heure}` : null,
                ].filter(Boolean);

                if (parts.length) {
                    info.el.title = parts.join(' | ');
                }
            },
        });

        calendar.render();
    });
</script>
@endsection
