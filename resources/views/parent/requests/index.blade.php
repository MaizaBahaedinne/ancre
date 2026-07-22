@extends('adminlte::page')

@section('title', 'Mes Demandes')

@section('content_header')
<div class="d-flex justify-content-between align-items-center">
    <div>
        <h1 class="m-0">Mes demandes et reclamations</h1>
        <small class="text-muted">Espace de communication avec l'administration</small>
    </div>
    <a href="{{ route('parent.demandes.create') }}" class="btn btn-primary">Nouvelle demande</a>
</div>
@stop

@section('content')
@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="card">
    <div class="card-body modern-table-card">
        <div class="table-responsive">
            <table class="table table-striped table-bordered js-data-table nowrap">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Type</th>
                        <th>Enfant</th>
                        <th>Sujet</th>
                        <th>Workflow</th>
                        <th width="150" class="no-sort">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($requests as $requestItem)
                        <tr>
                            <td>{{ optional($requestItem->created_at)->format('d/m/Y H:i') }}</td>
                            <td>{{ \App\Models\ParentRequest::ACTION_OPTIONS[$requestItem->action_type] ?? $requestItem->action_type }}</td>
                            <td>{{ $requestItem->enfant?->nom }} {{ $requestItem->enfant?->prenom }}</td>
                            <td>{{ $requestItem->subjectLabel() }}</td>
                            <td><span class="badge bg-{{ $requestItem->workflowBadgeClass() }}">{{ \App\Models\ParentRequest::STATUS_OPTIONS[$requestItem->workflow_status] ?? $requestItem->workflow_status }}</span></td>
                            <td>
                                <a href="{{ route('parent.demandes.show', $requestItem) }}" class="modern-action-btn is-view"><i class="fa-solid fa-eye"></i><span>Ouvrir</span></a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center">Aucune demande.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@stop