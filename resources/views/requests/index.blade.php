@extends('adminlte::page')

@section('title', 'Demandes Parents')

@section('content_header')
<h1 class="m-0">Demandes et reclamations des parents</h1>
@stop

@section('content')
@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="card mb-3">
    <div class="card-body">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-3">
                <label>Type</label>
                <select name="action_type" class="form-control">
                    <option value="">Tous</option>
                    @foreach(\App\Models\ParentRequest::ACTION_OPTIONS as $actionValue => $actionLabel)
                        <option value="{{ $actionValue }}" @selected($actionType === $actionValue)>{{ $actionLabel }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label>Workflow</label>
                <select name="workflow_status" class="form-control">
                    <option value="">Tous</option>
                    @foreach(\App\Models\ParentRequest::STATUS_OPTIONS as $statusValue => $statusLabel)
                        <option value="{{ $statusValue }}" @selected($workflowStatus === $statusValue)>{{ $statusLabel }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-5 d-flex gap-2">
                <button class="btn btn-primary">Filtrer</button>
                <a href="{{ route('demandes.index') }}" class="btn btn-outline-secondary">Reinitialiser</a>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body modern-table-card">
        <div class="table-responsive">
            <table class="table table-striped table-bordered js-data-table nowrap">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Parent</th>
                        <th>Enfant</th>
                        <th>Type</th>
                        <th>Sujet</th>
                        <th>Workflow</th>
                        <th width="120" class="no-sort">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($requests as $requestItem)
                        <tr>
                            <td>{{ optional($requestItem->created_at)->format('d/m/Y H:i') }}</td>
                            <td>{{ $requestItem->parent?->prenom }} {{ $requestItem->parent?->nom }}</td>
                            <td>{{ $requestItem->enfant?->prenom }} {{ $requestItem->enfant?->nom }}</td>
                            <td>{{ \App\Models\ParentRequest::ACTION_OPTIONS[$requestItem->action_type] ?? $requestItem->action_type }}</td>
                            <td>{{ $requestItem->subjectLabel() }}</td>
                            <td><span class="badge bg-{{ $requestItem->workflowBadgeClass() }}">{{ \App\Models\ParentRequest::STATUS_OPTIONS[$requestItem->workflow_status] ?? $requestItem->workflow_status }}</span></td>
                            <td>
                                <a href="{{ route('demandes.show', $requestItem) }}" class="modern-action-btn is-view"><i class="fa-solid fa-eye"></i><span>Ouvrir</span></a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center">Aucune demande.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@stop