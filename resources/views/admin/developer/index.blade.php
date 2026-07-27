@extends('adminlte::page')

@section('title', 'Developpeur - Deploiement')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h1 class="m-0">Espace developpeur</h1>
            <p class="text-muted mb-0">Checklist de deploiement et dernier apercu des logs serveur.</p>
        </div>
        <a href="{{ route('admin.developer.logs') }}" class="btn btn-outline-primary">Voir tous les logs</a>
    </div>
@stop

@section('content')
    <div class="row mb-4">
        <div class="col-lg-4 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted small mb-1">Etat logs</div>
                    <div class="h4 fw-bold mb-0">{{ $latestLog['name'] ?? 'Aucun fichier' }}</div>
                    <div class="text-muted small mt-2">{{ $latestLog ? number_format((int) ($latestLog['size'] / 1024), 1, ',', ' ') . ' Ko' : 'Aucun log detecte' }}</div>
                </div>
            </div>
        </div>
        <div class="col-lg-4 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted small mb-1">Entrees recentes</div>
                    <div class="h4 fw-bold mb-0">{{ count($latestEntries) }}</div>
                    <div class="text-muted small mt-2">Resume du dernier fichier de log.</div>
                </div>
            </div>
        </div>
        <div class="col-lg-4 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted small mb-1">Niveaux detectes</div>
                    <div class="d-flex flex-wrap gap-2 mt-2">
                        @forelse($latestLevelCounts as $level => $count)
                            <span class="badge bg-dark">{{ strtoupper($level) }}: {{ $count }}</span>
                        @empty
                            <span class="text-muted">Aucun log classe</span>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- SSL Certificate Card --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <h3 class="card-title mb-0">Certificat SSL</h3>
                    @if($ssl['reachable'] ?? false)
                        @if(($ssl['days_left'] ?? 0) > 30)
                            <span class="badge bg-success">Valide &mdash; {{ $ssl['days_left'] }} jours restants</span>
                        @elseif(($ssl['days_left'] ?? 0) > 7)
                            <span class="badge bg-warning text-dark">Expire bientot &mdash; {{ $ssl['days_left'] }} jours restants</span>
                        @else
                            <span class="badge bg-danger">Critique &mdash; {{ $ssl['days_left'] }} jours restants</span>
                        @endif
                    @else
                        <span class="badge bg-secondary">Inaccessible</span>
                    @endif
                </div>
                <div class="card-body">
                    @if(!($ssl['reachable'] ?? false))
                        <div class="alert alert-warning mb-0">
                            <i class="fas fa-exclamation-triangle me-1"></i>
                            Impossible de verifier le certificat SSL pour <strong>{{ $ssl['host'] }}</strong>.
                            @if(!empty($ssl['error']))
                                &mdash; {{ $ssl['error'] }}
                            @endif
                        </div>
                    @else
                        <div class="row g-3">
                            <div class="col-sm-6 col-lg-3">
                                <div class="text-muted small">Domaine</div>
                                <div class="fw-semibold">{{ $ssl['host'] }}</div>
                            </div>
                            <div class="col-sm-6 col-lg-3">
                                <div class="text-muted small">Emis par</div>
                                <div class="fw-semibold">{{ $ssl['issuer'] }}</div>
                            </div>
                            <div class="col-sm-6 col-lg-3">
                                <div class="text-muted small">Valide du</div>
                                <div class="fw-semibold">{{ $ssl['valid_from'] }}</div>
                            </div>
                            <div class="col-sm-6 col-lg-3">
                                <div class="text-muted small">Expire le</div>
                                <div class="fw-semibold @if(($ssl['days_left'] ?? 0) <= 30) text-danger @endif">{{ $ssl['valid_to'] }}</div>
                            </div>
                        </div>
                        @if(!empty($ssl['sans']))
                            <div class="mt-3">
                                <div class="text-muted small mb-1">Domaines couverts (SAN)</div>
                                <div class="d-flex flex-wrap gap-1">
                                    @foreach($ssl['sans'] as $san)
                                        <span class="badge bg-light text-dark border">{{ $san }}</span>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-7 mb-3">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <h3 class="card-title mb-0">Checklist de deploiement</h3>
                    <span class="badge bg-info">Lecture seule</span>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-3">
                        @foreach($deploymentSteps as $step)
                            <div class="border rounded p-3">
                                <div class="d-flex justify-content-between gap-3 flex-wrap align-items-start">
                                    <div>
                                        <div class="fw-semibold">{{ $step['title'] }}</div>
                                        <div class="text-muted small">{{ $step['description'] }}</div>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-outline-secondary" data-copy-command="{{ $step['command'] }}">Copier</button>
                                </div>
                                <pre class="bg-light rounded mt-3 mb-0 p-3 code-block"><code>{{ $step['command'] }}</code></pre>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-5 mb-3">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <h3 class="card-title mb-0">Derniers logs</h3>
                    <a href="{{ route('admin.developer.logs') }}" class="btn btn-sm btn-outline-primary">Ouvrir le journal</a>
                </div>
                <div class="card-body">
                    @forelse($latestEntries as $entry)
                        <div class="border rounded p-3 mb-3">
                            <div class="d-flex justify-content-between flex-wrap gap-2 mb-2">
                                <span class="badge {{ match($entry['level']) { 'error' => 'bg-danger', 'warning' => 'bg-warning text-dark', 'info' => 'bg-info text-dark', 'debug' => 'bg-secondary', default => 'bg-dark' } }}">{{ strtoupper($entry['level']) }}</span>
                                <small class="text-muted">{{ $entry['timestamp'] }}</small>
                            </div>
                            <div class="small text-muted mb-1">{{ $entry['channel'] }}</div>
                            <div class="log-snippet">{{ \Illuminate\Support\Str::limit($entry['message'], 220) }}</div>
                        </div>
                    @empty
                        <div class="text-muted">Aucun fichier de log disponible.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
    <style>
        .code-block {
            white-space: pre-wrap;
            word-break: break-word;
            font-size: 0.92rem;
        }

        .log-snippet {
            white-space: pre-wrap;
            word-break: break-word;
            font-size: 0.92rem;
        }
    </style>
@stop

@section('js')
    <script>
        (() => {
            document.querySelectorAll('[data-copy-command]').forEach((button) => {
                button.addEventListener('click', async () => {
                    const command = button.getAttribute('data-copy-command') || '';

                    try {
                        await navigator.clipboard.writeText(command);
                        button.textContent = 'Copie';
                        setTimeout(() => button.textContent = 'Copier', 1200);
                    } catch (error) {
                        button.textContent = 'Erreur';
                        setTimeout(() => button.textContent = 'Copier', 1200);
                    }
                });
            });
        })();
    </script>
@stop
