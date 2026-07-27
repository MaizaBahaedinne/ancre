@extends('adminlte::page')

@section('title', 'Éditer Workflow: ' . $notificationWorkflow->name)

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>Éditer Workflow</h1>
        <a href="{{ route('admin.notifications.workflows.show', $notificationWorkflow) }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Retour
        </a>
    </div>
@stop

@section('content')
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show">
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            <strong>Erreur de validation!</strong>
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card">
        <div class="card-header bg-primary">
            <h3 class="card-title">{{ $notificationWorkflow->name }}</h3>
        </div>

        <form action="{{ route('admin.notifications.workflows.update', $notificationWorkflow) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="card-body">
                <div class="form-group">
                    <label for="name">Nom du Workflow</label>
                    <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" 
                        value="{{ old('name', $notificationWorkflow->name) }}" required>
                    @error('name')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror" rows="4">{{ old('description', $notificationWorkflow->description) }}</textarea>
                    @error('description')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" id="is_enabled" name="is_enabled" value="1"
                            {{ old('is_enabled', $notificationWorkflow->is_enabled) ? 'checked' : '' }}>
                        <label class="custom-control-label" for="is_enabled">
                            Workflow Actif
                        </label>
                    </div>
                    <small class="form-text text-muted">
                        Décochez pour désactiver temporairement ce workflow
                    </small>
                </div>

                <div class="alert alert-info">
                    <strong><i class="fas fa-info-circle"></i> Information</strong>
                    <ul class="mb-0 mt-2">
                        <li>Trigger: <code>{{ $notificationWorkflow->trigger }}</code></li>
                        <li>Module: <span class="badge bg-info">{{ $notificationWorkflow->module }}</span></li>
                        <li>Créé: {{ $notificationWorkflow->created_at->format('d/m/Y H:i') }}</li>
                    </ul>
                </div>
            </div>

            <div class="card-footer">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Mettre à jour
                </button>
                <a href="{{ route('admin.notifications.workflows.show', $notificationWorkflow) }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Annuler
                </a>
            </div>
        </form>
    </div>
@stop

@section('css')
    <style>
        .badge { padding: 0.35rem 0.6rem; }
        code { background-color: #f4f4f4; padding: 0.2rem 0.4rem; border-radius: 3px; }
    </style>
@stop
