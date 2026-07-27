@extends('adminlte::page')

@section('title', 'Workflows de Notifications')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>Workflows de Notifications</h1>
    </div>
@stop

@section('content')
    @if ($message = Session::get('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            <strong>Succès!</strong> {{ $message }}
        </div>
    @endif

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Liste des Workflows</h3>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Trigger</th>
                            <th>Nom</th>
                            <th>Module</th>
                            <th>Description</th>
                            <th>Receivers</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($workflows as $workflow)
                            <tr>
                                <td>
                                    <code style="font-size: 0.85rem;">{{ $workflow->trigger }}</code>
                                </td>
                                <td><strong>{{ $workflow->name }}</strong></td>
                                <td>
                                    <span class="badge bg-info">{{ $workflow->module }}</span>
                                </td>
                                <td>{{ Str::limit($workflow->description, 50) }}</td>
                                <td>
                                    <span class="badge bg-primary">{{ $workflow->receivers->count() }}</span>
                                </td>
                                <td>
                                    @if($workflow->is_enabled)
                                        <span class="badge bg-success">Activé</span>
                                    @else
                                        <span class="badge bg-secondary">Désactivé</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('admin.notifications.workflows.show', $workflow) }}" class="btn btn-sm btn-info" title="Voir">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.notifications.workflows.edit', $workflow) }}" class="btn btn-sm btn-warning" title="Éditer">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">
                                    Aucun workflow trouvé
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@stop

@section('css')
    <style>
        .badge { padding: 0.35rem 0.6rem; }
        .btn-sm { padding: 0.25rem 0.5rem; }
    </style>
@stop
