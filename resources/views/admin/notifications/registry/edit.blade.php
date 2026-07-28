@extends('adminlte::page')

@section('title', 'Edit Trigger Override')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>Override Trigger: <code>{{ $definition['trigger'] }}</code></h1>
        <a href="{{ route('admin.notifications.registry.index') }}" class="btn btn-secondary">Retour</a>
    </div>
@stop

@section('content')
    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card">
        <div class="card-header"><strong>Valeurs actuelles (registry)</strong></div>
        <div class="card-body">
            <p><strong>Nom:</strong> {{ $definition['name'] ?? '-' }}</p>
            <p><strong>Description:</strong> {{ $definition['description'] ?? '-' }}</p>
            <p><strong>Module:</strong> {{ $definition['module'] ?? 'general' }}</p>
            <p><strong>Active:</strong> {{ !empty($definition['is_enabled']) ? 'Oui' : 'Non' }}</p>
        </div>
    </div>

    <div class="card">
        <div class="card-header"><strong>Override en base de donnees</strong></div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.notifications.registry.update', ['trigger' => $definition['trigger']]) }}">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label class="form-label">Nom (optionnel)</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name', $override?->name) }}">
                </div>

                <div class="mb-3">
                    <label class="form-label">Description (optionnel)</label>
                    <textarea name="description" class="form-control" rows="3">{{ old('description', $override?->description) }}</textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">Module (optionnel)</label>
                    <input type="text" name="module" class="form-control" value="{{ old('module', $override?->module) }}">
                </div>

                <div class="mb-3">
                    <label class="form-label">Etat active</label>
                    @php
                        $mode = 'inherit';
                        if (!is_null($override?->is_enabled)) {
                            $mode = $override->is_enabled ? 'enabled' : 'disabled';
                        }
                    @endphp
                    <select name="enabled_mode" class="form-control">
                        <option value="inherit" {{ old('enabled_mode', $mode) === 'inherit' ? 'selected' : '' }}>Heriter du registry</option>
                        <option value="enabled" {{ old('enabled_mode', $mode) === 'enabled' ? 'selected' : '' }}>Forcer Active</option>
                        <option value="disabled" {{ old('enabled_mode', $mode) === 'disabled' ? 'selected' : '' }}>Forcer Inactive</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Receivers JSON (optionnel)</label>
                    <textarea name="receivers_json" class="form-control" rows="6" placeholder='[{"receiver_type":"role","receiver_value":"Administrateur","notification_medium":"all","is_enabled":true}]'>{{ old('receivers_json', $override?->receivers ? json_encode($override->receivers, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE) : '') }}</textarea>
                </div>

                <button class="btn btn-primary" type="submit">Enregistrer Override</button>
            </form>
        </div>
    </div>
@stop
