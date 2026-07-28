@extends('adminlte::page')

@section('title', 'Logs Notifications')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>Logs Notifications</h1>
        <a href="{{ route('admin.notifications.workflows.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left"></i> Retour Workflows
        </a>
    </div>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <strong>Historique des envois (max 500)</strong>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover w-100 js-data-table" id="notification-logs-table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Trigger</th>
                            <th>Canal</th>
                            <th>Statut</th>
                            <th>Destinataire</th>
                            <th>Utilisateur cible</th>
                            <th>Sujet</th>
                            <th>Erreur</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($logs as $log)
                            @php
                                $notification = $log->notification;
                                $targetUser = $notification?->user;
                            @endphp
                            <tr>
                                <td>{{ $log->created_at?->format('d/m/Y H:i:s') }}</td>
                                <td><code>{{ $notification?->trigger ?? '-' }}</code></td>
                                <td>
                                    <span class="badge bg-info text-dark">{{ strtoupper($log->channel ?? '-') }}</span>
                                </td>
                                <td>
                                    @php
                                        $status = strtolower((string) $log->status);
                                        $statusClass = $status === 'sent' ? 'bg-success' : ($status === 'failed' ? 'bg-danger' : 'bg-secondary');
                                    @endphp
                                    <span class="badge {{ $statusClass }}">{{ strtoupper($log->status ?? 'unknown') }}</span>
                                </td>
                                <td>{{ $log->recipient ?? '-' }}</td>
                                <td>
                                    @if($targetUser)
                                        {{ $targetUser->name }} (ID: {{ $targetUser->id }})
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>{{ $notification?->subject ?? '-' }}</td>
                                <td>{{ $log->error_message ? \Illuminate\Support\Str::limit($log->error_message, 120) : '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">Aucun log de notification disponible.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@stop
