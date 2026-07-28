@extends('adminlte::page')

@section('title', 'TriggerRegistry')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>TriggerRegistry</h1>
        <form method="POST" action="{{ route('admin.notifications.registry.sync') }}">
            @csrf
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-rotate"></i> Synchroniser vers Workflows
            </button>
        </form>
    </div>
@stop

@section('content')
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card">
        <div class="card-header">
            <strong>Triggers detectes ({{ $definitions->count() }})</strong>
        </div>
        <div class="card-body table-responsive">
            <table id="registry-table" class="table table-striped align-middle w-100">
                <thead>
                    <tr>
                        <th>Trigger</th>
                        <th>Nom</th>
                        <th>Module</th>
                        <th>Active</th>
                        <th>Receivers</th>
                        <th>Override</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($definitions as $def)
                        @php $hasOverride = $overrides->has($def['trigger']); @endphp
                        <tr>
                            <td><code>{{ $def['trigger'] }}</code></td>
                            <td>{{ $def['name'] ?? '-' }}</td>
                            <td><span class="badge bg-info">{{ $def['module'] ?? 'general' }}</span></td>
                            <td>
                                @if(!empty($def['is_enabled']))
                                    <span class="badge bg-success">Oui</span>
                                @else
                                    <span class="badge bg-secondary">Non</span>
                                @endif
                            </td>
                            <td>{{ is_array($def['receivers'] ?? null) ? count($def['receivers']) : 0 }}</td>
                            <td>
                                @if($hasOverride)
                                    <span class="badge bg-warning text-dark">DB Override</span>
                                @else
                                    <span class="badge bg-light text-dark">Defaut</span>
                                @endif
                            </td>
                            <td class="d-flex gap-1">
                                <a href="{{ route('admin.notifications.registry.edit', ['trigger' => $def['trigger']]) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-pen"></i> Editer
                                </a>
                                @if($hasOverride)
                                    <form method="POST" action="{{ route('admin.notifications.registry.destroy', ['trigger' => $def['trigger']]) }}" onsubmit="return confirm('Supprimer cet override ?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center text-muted">Aucun trigger detecte.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@stop

@section('js')
    <script src="https://cdn.datatables.net/2.3.4/js/dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/2.3.4/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.2.5/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.2.5/js/buttons.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.2.5/js/buttons.html5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            if (typeof DataTable === 'undefined') {
                return;
            }

            new DataTable('#registry-table', {
                order: [[0, 'asc']],
                pageLength: 25,
                lengthMenu: [10, 25, 50, 100],
                responsive: true,
                layout: {
                    topStart: {
                        buttons: [
                            { extend: 'csvHtml5', text: 'Exporter CSV', className: 'btn btn-sm btn-outline-primary' }
                        ]
                    }
                },
                language: {
                    search: 'Rechercher:',
                    lengthMenu: 'Afficher _MENU_ elements',
                    info: 'Affichage _START_ a _END_ sur _TOTAL_ elements',
                    infoEmpty: 'Aucun element a afficher',
                    zeroRecords: 'Aucun resultat trouve',
                    paginate: {
                        first: 'Premier',
                        last: 'Dernier',
                        next: 'Suivant',
                        previous: 'Precedent'
                    }
                },
                columnDefs: [
                    { orderable: false, targets: [6] }
                ]
            });
        });
    </script>
@stop
