@extends('adminlte::page')

@section('title', 'Sujets Demandes')

@section('content_header')
<div class="d-flex justify-content-between align-items-center">
    <h1 class="m-0">Sujets demandes/reclamations</h1>
    <a href="{{ route('demandes-sujets.create') }}" class="btn btn-primary">Nouveau sujet</a>
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
                        <th>Type</th>
                        <th>Sujet</th>
                        <th>Ordre</th>
                        <th>Statut</th>
                        <th width="200" class="no-sort">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($subjects as $subject)
                        <tr>
                            <td>{{ \App\Models\ParentRequest::ACTION_OPTIONS[$subject->action_type] ?? $subject->action_type }}</td>
                            <td>{{ $subject->label }}</td>
                            <td>{{ $subject->sort_order }}</td>
                            <td><span class="badge bg-{{ $subject->is_active ? 'success' : 'secondary' }}">{{ $subject->is_active ? 'Actif' : 'Inactif' }}</span></td>
                            <td>
                                @can('requests.subjects.manage')
                                    <div class="modern-action-group">
                                        <a href="{{ route('demandes-sujets.edit', $subject) }}" class="modern-action-btn is-edit"><i class="fa-solid fa-pen"></i><span>Modifier</span></a>
                                        <form method="POST" action="{{ route('demandes-sujets.destroy', $subject) }}" class="modern-inline-form" onsubmit="return confirm('Supprimer ce sujet ?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="modern-action-btn is-delete" type="submit"><i class="fa-solid fa-trash"></i><span>Supprimer</span></button>
                                        </form>
                                    </div>
                                @else
                                    <span class="text-muted">-</span>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center">Aucun sujet configure.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@stop