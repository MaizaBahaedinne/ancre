@extends('adminlte::page')

@section('title', $notificationWorkflow->name)

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>{{ $notificationWorkflow->name }}</h1>
        <a href="{{ route('admin.notifications.workflows.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Retour
        </a>
    </div>
@stop

@section('content')
    @if ($message = Session::get('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            <strong>Succès!</strong> {{ $message }}
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show">
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            <strong>Erreur!</strong>
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row">
        <!-- Détails du Workflow -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-primary">
                    <h3 class="card-title">Informations Workflow</h3>
                </div>
                <div class="card-body">
                    <dl class="row">
                        <dt class="col-sm-4">Trigger:</dt>
                        <dd class="col-sm-8">
                            <code>{{ $notificationWorkflow->trigger }}</code>
                        </dd>

                        <dt class="col-sm-4">Nom:</dt>
                        <dd class="col-sm-8">
                            <strong>{{ $notificationWorkflow->name }}</strong>
                        </dd>

                        <dt class="col-sm-4">Module:</dt>
                        <dd class="col-sm-8">
                            <span class="badge bg-info">{{ $notificationWorkflow->module }}</span>
                        </dd>

                        <dt class="col-sm-4">Status:</dt>
                        <dd class="col-sm-8">
                            @if($notificationWorkflow->is_enabled)
                                <span class="badge bg-success">Activé</span>
                            @else
                                <span class="badge bg-secondary">Désactivé</span>
                            @endif
                        </dd>

                        <dt class="col-sm-4">Description:</dt>
                        <dd class="col-sm-8">
                            {{ $notificationWorkflow->description ?? 'N/A' }}
                        </dd>

                        <dt class="col-sm-4">Créé:</dt>
                        <dd class="col-sm-8">
                            {{ $notificationWorkflow->created_at->format('d/m/Y H:i') }}
                        </dd>
                    </dl>

                    <div class="mt-3">
                        <a href="{{ route('admin.notifications.workflows.edit', $notificationWorkflow) }}" class="btn btn-warning">
                            <i class="fas fa-edit"></i> Éditer
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistiques -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-success">
                    <h3 class="card-title">Statistiques</h3>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-6">
                            <h5>Total Receivers</h5>
                            <h2 class="text-primary">{{ $notificationWorkflow->receivers->count() }}</h2>
                        </div>
                        <div class="col-md-6">
                            <h5>Actifs</h5>
                            <h2 class="text-success">{{ $notificationWorkflow->receivers->where('is_enabled', true)->count() }}</h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Receivers -->
    <div class="card mt-4">
        <div class="card-header bg-info">
            <h3 class="card-title">Receivers</h3>
            <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                    <i class="fas fa-minus"></i>
                </button>
            </div>
        </div>
        <div class="card-body">
            @if($notificationWorkflow->receivers->count() > 0)
                <div class="table-responsive">
                    <table class="table table-striped table-sm">
                        <thead>
                            <tr>
                                <th>Type</th>
                                <th>Valeur</th>
                                <th>Canal</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($notificationWorkflow->receivers as $receiver)
                                <tr>
                                    <td>
                                        <span class="badge bg-primary">{{ ucfirst($receiver->receiver_type) }}</span>
                                    </td>
                                    <td>
                                        <code>{{ $receiver->receiver_value ?? '-' }}</code>
                                    </td>
                                    <td>
                                        <span class="badge bg-warning">{{ $receiver->notification_medium }}</span>
                                    </td>
                                    <td>
                                        @if($receiver->is_enabled)
                                            <span class="badge bg-success">Actif</span>
                                        @else
                                            <span class="badge bg-secondary">Inactif</span>
                                        @endif
                                    </td>
                                    <td>
                                        <form action="{{ route('admin.notifications.receivers.toggle', $receiver) }}" method="POST" style="display:inline;">
                                            @csrf
                                            <button type="submit" class="btn btn-xs btn-outline-secondary" title="Basculer status">
                                                <i class="fas fa-toggle-on"></i>
                                            </button>
                                        </form>

                                        <form action="{{ route('admin.notifications.receivers.destroy', $receiver) }}" method="POST" style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-xs btn-outline-danger" title="Supprimer" onclick="return confirm('Êtes-vous sûr?')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-muted">Aucun receiver configuré</p>
            @endif
        </div>
    </div>

    <!-- Ajouter Receiver -->
    <div class="card mt-4">
        <div class="card-header bg-success">
            <h3 class="card-title">Ajouter un Receiver</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.notifications.receivers.store', $notificationWorkflow) }}" method="POST">
                @csrf

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="receiver_type">Type de Receiver</label>
                            <select name="receiver_type" id="receiver_type" class="form-control @error('receiver_type') is-invalid @enderror" required>
                                <option value="">-- Sélectionner --</option>
                                <option value="role">Rôle</option>
                                <option value="user">Utilisateur</option>
                                <option value="default">Défaut (tous)</option>
                            </select>
                            @error('receiver_type')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="receiver_value">Valeur</label>
                            <input type="text" name="receiver_value" id="receiver_value" class="form-control @error('receiver_value') is-invalid @enderror" placeholder="Rôle ou ID utilisateur">
                            @error('receiver_value')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                            <small class="form-text text-muted mt-1">
                                Exemple: "Administrateur", "Parent", ou ID utilisateur
                            </small>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="notification_medium">Canal de Notification</label>
                            <select name="notification_medium" id="notification_medium" class="form-control @error('notification_medium') is-invalid @enderror" required>
                                <option value="">-- Sélectionner --</option>
                                <option value="system">Système (Badge)</option>
                                <option value="email">Email</option>
                                <option value="sms">SMS</option>
                                <option value="all">Tous les canaux</option>
                            </select>
                            @error('notification_medium')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-plus"></i> Ajouter Receiver
                    </button>
                </div>
            </form>
        </div>
    </div>
@stop

@section('css')
    <style>
        .badge { padding: 0.35rem 0.6rem; }
        .btn-xs { padding: 0.25rem 0.4rem; font-size: 0.75rem; }
        code { background-color: #f4f4f4; padding: 0.2rem 0.4rem; border-radius: 3px; }
    </style>
@stop
