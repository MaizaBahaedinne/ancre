@extends('adminlte::page')

@section('title', 'Developpeur - Logs')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h1 class="m-0">Logs systeme</h1>
            <p class="text-muted mb-0">Vue structuree des actions et erreurs du serveur.</p>
        </div>
        <a href="{{ route('admin.developer.index') }}" class="btn btn-outline-secondary">Retour deploiement</a>
    </div>
@stop

@section('content')
    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.developer.logs') }}" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label">Fichier</label>
                    <select name="file" class="form-control">
                        @foreach($logFiles as $logFile)
                            <option value="{{ $logFile['name'] }}" @selected(($selectedLog['name'] ?? null) === $logFile['name'])>
                                {{ $logFile['name'] }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Niveau</label>
                    <select name="level" class="form-control">
                        <option value="">Tous</option>
                        @foreach(['error', 'warning', 'info', 'debug', 'critical', 'notice', 'alert', 'emergency'] as $candidateLevel)
                            <option value="{{ $candidateLevel }}" @selected($level === $candidateLevel)>{{ strtoupper($candidateLevel) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Recherche</label>
                    <input type="text" name="search" value="{{ $search }}" class="form-control" placeholder="Rechercher un message, une route, un user...">
                </div>
                <div class="col-md-2 d-grid">
                    <button type="submit" class="btn btn-primary">Filtrer</button>
                </div>
            </form>
        </div>
    </div>

    <div class="row mb-3">
        @foreach($levelCounts as $countLevel => $count)
            <div class="col-md-2 col-6 mb-2">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body text-center">
                        <div class="small text-muted">{{ strtoupper($countLevel) }}</div>
                        <div class="h4 fw-bold mb-0">{{ $count }}</div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="card">
        <div class="card-body">
            @forelse($groupedEntries as $date => $dailyEntries)
                <div class="mb-4">
                    <h3 class="h5 mb-3">{{ \Illuminate\Support\Carbon::parse($date)->format('d/m/Y') }}</h3>

                    @foreach($dailyEntries as $entry)
                        <div class="border rounded p-3 mb-3">
                            <div class="d-flex justify-content-between flex-wrap gap-2 mb-2">
                                <div class="d-flex gap-2 flex-wrap align-items-center">
                                    <span class="badge {{ match($entry['level']) { 'error' => 'bg-danger', 'warning' => 'bg-warning text-dark', 'info' => 'bg-info text-dark', 'debug' => 'bg-secondary', default => 'bg-dark' } }}">{{ strtoupper($entry['level']) }}</span>
                                    <span class="text-muted small">{{ $entry['channel'] }}</span>
                                </div>
                                <small class="text-muted">{{ $entry['timestamp'] }}</small>
                            </div>
                            <pre class="mb-0 log-entry">{{ $entry['message'] }}</pre>
                        </div>
                    @endforeach
                </div>
            @empty
                <div class="text-muted">Aucune entree ne correspond aux filtres selectionnes.</div>
            @endforelse

            @if(($selectedLog['name'] ?? null) === null)
                <div class="text-muted">Aucun fichier de log detecte dans storage/logs.</div>
            @endif
        </div>
    </div>
@stop

@section('css')
    <style>
        .log-entry {
            white-space: pre-wrap;
            word-break: break-word;
            margin-bottom: 0;
            font-size: 0.92rem;
        }
    </style>
@stop
